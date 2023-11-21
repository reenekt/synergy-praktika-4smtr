<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head, router} from '@inertiajs/vue3';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { useForm, usePage } from '@inertiajs/vue3';
import { QuillEditor } from '@vueup/vue-quill'
import '@vueup/vue-quill/dist/vue-quill.snow.css';
import VueMultiselect from 'vue-multiselect'
import 'vue-multiselect/dist/vue-multiselect.css'
import {ref} from "vue";
import Checkbox from "@/Components/Checkbox.vue";

const props = defineProps({
    post: {
        type: Object
    },
    tags: {
        type: Array,
    },
});

const user = usePage().props.auth.user;

const form = useForm({
    title: props.post.title,
    content: props.post.content,
    user_id: props.post.user_id,
    is_private: props.post.is_private,
    tags: props.post.tags,
});

function addTag(tagName) {

    if (!props.tags.find(tag => tag.name === tagName)) {
        const data = {
            name: tagName,
        }
        axios.post(route('tags.store'), data)
            .then((response) => {
                const tagId = response.data.id
                console.log('tagId', tagId)

                router.reload({
                    only: ['tags'],
                    onSuccess: () => {
                        console.log('props.tags', props.tags, props.tags.value)

                        const tag = props.tags.find(tag => tag.id === tagId)

                        form.tags.push(tag)
                    }
                })
            })
    }
}

function submitForm() {
    form
        .transform((data) => {
            data.tags = data.tags.map((tag) => tag.id)
            return data
        })
        .patch(route('posts.update', {post: props.post.id}))
}
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Изменить пост</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                    <form @submit.prevent="submitForm" class="mt-6 space-y-6">
                        <div>
                            <InputLabel for="name" value="Заголовок" />

                            <TextInput
                                id="title"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="form.title"
                                required
                                autofocus
                            />

                            <InputError class="mt-2" :message="form.errors.title" />
                        </div>

                        <div>
                            <InputLabel for="content" value="Содержимое" />

                            <QuillEditor
                                id="content"
                                theme="snow"
                                content-type="html"
                                :toolbar="[
                                    [{ 'header': [1, 2, 3, false] }],
                                    ['bold', 'italic', 'underline', 'strike'],
                                    ['blockquote', 'code-block'],
                                ]"
                                v-model:content="form.content"
                            ></QuillEditor>

                            <InputError class="mt-2" :message="form.errors.content" />
                        </div>

                        <div>
                            <label class="flex items-center">
                                <Checkbox name="remember" v-model:checked="form.is_private" />
                                <span class="ms-2 text-sm text-gray-600">Скрытый пост</span>
                            </label>
                            <InputError class="mt-2" :message="form.errors.is_private" />
                        </div>

                        <div>
                            <InputLabel for="content" value="Теги" />

                            <VueMultiselect
                                v-model="form.tags"
                                :options="tags"
                                :multiple="true"
                                :taggable="true"
                                @tag="addTag"
                                tag-placeholder="Добавить тег"
                                placeholder="Начните вводить для выбора или добавления нового тега"
                                label="name"
                                track-by="id"
                            />

                            <InputError class="mt-2" :message="form.errors.tags" />
                        </div>

                        <div class="flex items-center gap-4">
                            <PrimaryButton :disabled="form.processing">Сохранить</PrimaryButton>

                            <Transition
                                enter-active-class="transition ease-in-out"
                                enter-from-class="opacity-0"
                                leave-active-class="transition ease-in-out"
                                leave-to-class="opacity-0"
                            >
                                <p v-if="form.recentlySuccessful" class="text-sm text-gray-600">Изменения сохранены.</p>
                            </Transition>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
