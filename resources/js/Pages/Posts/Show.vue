<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head, Link, router, useForm, usePage} from '@inertiajs/vue3';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/TextInput.vue';
import TextAreaInput from '@/Components/TextAreaInput.vue';
import { QuillEditor } from '@vueup/vue-quill'
import '@vueup/vue-quill/dist/vue-quill.snow.css';
import VueMultiselect from 'vue-multiselect'
import 'vue-multiselect/dist/vue-multiselect.css'
import InputError from "@/Components/InputError.vue";
import PrimaryButton from "@/Components/PrimaryButton.vue";
import Checkbox from "@/Components/Checkbox.vue";

const props = defineProps({
    post: {
        type: Object,
    },
    users: {
        type: Array,
    },
});

const form = useForm({
    post_id: props.post.id,
    user_id: usePage().props.auth.user.id,
    content: null,
});
const grandAccessForm = useForm({
    user_id: null,
});

function submitForm() {
    form.post(route('comments.store'), {
        onSuccess: () => {
            router.reload({
                only: ['comments'],
            })
        }
    })
}

function submitGrandAccessForm() {
    grandAccessForm
        .transform((data) => {
            data.user_id = data.user_id.id
            return data
        })
        .post(route('posts.grandAccess', {post: props.post.id}), {
            onSuccess: () => {
                router.reload({
                    only: ['post']
                })
            }
        })
}

function formatDate(date) {
    return `${date.getDate()}.${date.getMonth() + 1}.${date.getFullYear()} ${date.getMinutes()}:${date.getHours()}`
}
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Детальная страница поста #{{ post.id }}</h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
                <!-- Данные поста -->
                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                    <div class="space-y-6">
                        <div>
                            <InputLabel for="name" value="Заголовок" />

                            <TextInput
                                id="title"
                                type="text"
                                class="mt-1 block w-full"
                                v-model="post.title"
                                required
                                autofocus
                                disabled
                            />
                        </div>

                        <div>
                            <InputLabel for="content" value="Содержимое" />

                            <QuillEditor
                                id="content"
                                theme="snow"
                                content-type="html"
                                :toolbar="'none'"
                                :content="post.content"
                                read-only
                            ></QuillEditor>
                        </div>

                        <div>
                            <label class="flex items-center">
                                <Checkbox name="remember" :checked="post.is_private" disabled />
                                <span class="ms-2 text-sm text-gray-600">Скрытый пост</span>
                            </label>
                        </div>

                        <div>
                            <InputLabel for="content" value="Теги" />

                            <VueMultiselect
                                v-model="post.tags"
                                :options="post.tags"
                                :multiple="true"
                                :taggable="true"
                                tag-placeholder="Добавить тег"
                                placeholder="Начните вводить для выбора или добавления нового тега"
                                label="name"
                                track-by="id"
                                disabled
                            />
                        </div>

                        <div v-if="$page.props.auth.check && post.user_id === $page.props.auth.user.id" class="flex items-center gap-4">
                            <Link
                                class="no-underline text-base p-2 text-gray-600 hover:text-gray-100 transition-colors rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 bg-gray-300 hover:bg-gray-400 hover:shadow-lg transition-shadow"
                                :href="route('posts.edit', {post: post.id})"
                                as="button"
                                type="button">Изменить
                            </Link>
                            <Link
                                class="ml-6 no-underline text-base p-2 text-red-100 hover:text-white transition-colors rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 bg-red-500 hover:bg-red-700 hover:shadow-lg transition-shadow"
                                :href="route('posts.destroy', {post: post.id})"
                                method="delete"
                                as="button"
                                type="button"
                            >Удалить</Link>
                        </div>
                    </div>
                </div>

                <!-- Доступ к скрытому посту -->
                <div v-if="post.is_private" class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                    <div class="space-y-6">
                        <h2 class="text-xl">Предоставить доступ к скрытому посту</h2>
                        <div class="mt-4">
                            <form @submit.prevent="submitGrandAccessForm" class="space-y-4">
                                <div>
                                    <InputLabel for="content" value="Выберите пользователя" />

                                    <VueMultiselect
                                        v-model="grandAccessForm.user_id"
                                        :options="users"
                                        :multiple="false"
                                        placeholder="Выбрать пользователя"
                                        label="name"
                                        track-by="id"
                                        required
                                    />
                                </div>

                                <div class="flex items-center gap-4">
                                    <PrimaryButton :disabled="grandAccessForm.processing || grandAccessForm.user_id === null" class="disabled:opacity-25">Предоставить доступ</PrimaryButton>

                                    <Transition
                                        enter-active-class="transition ease-in-out"
                                        enter-from-class="opacity-0"
                                        leave-active-class="transition ease-in-out"
                                        leave-to-class="opacity-0"
                                    >
                                        <p v-if="grandAccessForm.recentlySuccessful" class="text-sm text-gray-600">Доступ предоставлен.</p>
                                    </Transition>
                                </div>

                                <div>


                                    <VueMultiselect
                                        class="mt-2"
                                        :model-value="post.private_access_approved_users || []"
                                        :options="post.private_access_approved_users || []"
                                        :multiple="true"
                                        :taggable="true"
                                        tag-placeholder="-"
                                        placeholder="Нет пользователей, имеющих доступ"
                                        label="name"
                                        track-by="id"
                                        disabled
                                    />
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Комментарии -->
                <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                    <div class="space-y-6">
                        <h2 class="text-xl">Комментарии</h2>
                        <div class="mt-4">
                            <form @submit.prevent="submitForm" class="space-y-4">
                                <div>
                                    <InputLabel for="name" value="Оставить комментарий" />

                                    <TextAreaInput
                                        id="title"
                                        type="text"
                                        class="mt-1 block w-full"
                                        v-model="form.content"
                                        required
                                        autofocus
                                        placeholder="Введите комментарий"
                                    />

                                    <InputError class="mt-2" :message="form.errors.content" />
                                </div>

                                <div class="flex items-center gap-4">
                                    <PrimaryButton :disabled="form.processing">Отправить</PrimaryButton>

                                    <Transition
                                        enter-active-class="transition ease-in-out"
                                        enter-from-class="opacity-0"
                                        leave-active-class="transition ease-in-out"
                                        leave-to-class="opacity-0"
                                    >
                                        <p v-if="form.recentlySuccessful" class="text-sm text-gray-600">Комментарий добавлен</p>
                                    </Transition>
                                </div>
                            </form>
                        </div>
                    </div>
                    <hr v-if="post.comments.length > 0" class="my-6">
                    <div>
                        <div v-for="comment in post.comments" :key="comment.id" class="flex my-8">
                            <div class="basis-1/4 self-start">
                                <div>{{ comment.user.name }}</div>
                                <div>{{ formatDate(new Date(comment.created_at)) }}</div>
                                <div v-if="comment.user.id === $page.props.auth.user.id">
                                    <Link
                                        class="mt-4 no-underline text-base p-2 text-red-100 hover:text-white transition-colors rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 bg-red-500 hover:bg-red-700 hover:shadow-lg transition-shadow"
                                        :href="route('comments.destroy', {comment: comment.id})"
                                        method="delete"
                                        as="button"
                                        type="button"
                                    >Удалить</Link>
                                </div>
                            </div>
                            <div class="basis-full">
                                <TextAreaInput
                                    id="title"
                                    type="text"
                                    class="mt-1 block w-full resize-none"
                                    :model-value="comment.content"
                                    autofocus
                                    disabled
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
