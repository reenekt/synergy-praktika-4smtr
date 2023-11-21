<script setup>
import {Head, Link} from "@inertiajs/vue3";
import AuthenticatedLayout from "@/Layouts/AuthenticatedLayout.vue";
import Tag from "@/Components/Tag.vue";

const props = defineProps({
    tags: {
        type: Array,
    },
})
</script>

<template>
    <Head title="Теги" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">{{ pageTitle || 'Теги' }}</h2>
        </template>

        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 py-6">
            <div class="p-6 bg-white shadow-sm sm:rounded-lg flex flex-wrap">
                <Link v-for="tag in tags.data" :key="tag.id" :href="route('posts.indexByTag', {_query: {tag_id: tag.id}})">
                    <tag class="hover:border-gray-300 transition-colors">#{{ tag.name }}</tag>
                </Link>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
