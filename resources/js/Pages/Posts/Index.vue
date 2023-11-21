<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import {Head, Link} from '@inertiajs/vue3';
import {computed} from "vue";
import Tag from "@/Components/Tag.vue";

const props = defineProps({
    posts: {
        type: Array,
    },
    pageTitle: {
        type: String,
    },
    hideSubscribeButton: {
        type: Boolean,
        default: false,
    },
})

function formatDate(date) {
    return `${date.getDate()}.${date.getMonth() + 1}.${date.getFullYear()} ${date.getMinutes()}:${date.getHours()}`
}
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ pageTitle || 'Посты' }}</h2>
        </template>

        <div v-if="$page.props.auth.check" class="max-w-7xl mx-auto sm:px-6 lg:px-8 mt-8">
            <div class="pb-2 text-sm mt-2 text-gray-800">
                <Link
                    :href="route('posts.create')"
                    method="get"
                    as="button"
                    class="no-underline text-base p-4 text-gray-600 hover:text-gray-100 transition-colors rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 bg-gray-300 hover:bg-gray-400 hover:shadow-lg transition-shadow"
                >
                    Создать пост
                </Link>
            </div>
        </div>

        <article v-for="(post, i) in posts.data" :key="i" class="py-6">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-lg transition-shadow">
                    <Link :href="route('posts.show', {post: post.id})">
                        <h1 class="p-6 text-gray-900 text-xl font-bold hover:text-gray-600">{{ post.title }}</h1>
                    </Link>

                    <div class="p-6 text-gray-900 text-ellipsis max-h-28 overflow-hidden" v-html="post.content"></div>

                    <template v-if="post.user">
                        <hr>
                        <div class="p-6 pb-3 text-blue-600 text-sm flex justify-between">
                            <div class="flex">
                                <div class="flex">
                                    <tag
                                        v-for="tag in post.tags.slice(0, 3)"
                                        :key="tag.id"
                                    >#{{ tag.name }}</tag>
                                    <tag v-if="post.tags.length > 3">+{{ post.tags.length - 3 }}</tag>
                                </div>
                                <Link v-if="post.comments.length > 0" :href="route('posts.show', {post: post.id})" class="self-center ml-4 hover:text-blue-400">
                                    <div>Комментариев: {{ post.comments.length }}</div>
                                </Link>
                            </div>
                            <div class="flex justify-end">
                                <template v-if="$page.props.auth.check && post.user.id !== $page.props.auth.user.id">
                                    <Link
                                        v-if="!hideSubscribeButton && !post.user.subscribers?.find((subscriber) => subscriber.id === $page.props.auth.user.id)"
                                        :href="route('subscription.store')"
                                        method="post"
                                        :data="{subscriber_id: $page.props.auth.user.id, target_id: post.user.id}"
                                        as="button"
                                        class="no-underline text-base p-2 text-gray-600 hover:text-gray-100 transition-colors rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 bg-gray-300 hover:bg-gray-400 hover:shadow-lg transition-shadow"
                                    >
                                        Подписаться
                                    </Link>
                                    <template v-else>
                                        <p class="text-gray-600 self-center">Вы подписаны</p>
                                        <Link
                                            :href="route('subscription.destroyByUsers')"
                                            method="post"
                                            :data="{subscriber_id: $page.props.auth.user.id, target_id: post.user.id}"
                                            as="button"
                                            class="ml-6 no-underline text-base p-2 text-gray-600 hover:text-gray-100 transition-colors rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 bg-gray-300 hover:bg-gray-400 hover:shadow-lg transition-shadow"
                                        >
                                            Отписаться
                                        </Link>
                                    </template>
                                </template>
                                <p class="ml-6 self-center">{{ post.user?.name || 'Неизвестный автор' }} | {{ formatDate(new Date(post.created_at)) }}</p>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </article>
    </AuthenticatedLayout>
</template>
