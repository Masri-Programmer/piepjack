<template>
    <PageLayout
        :data="links"
        :title="data?.data?.name"
        :isLoading="isLoading"
        :error="error"
    >
        <CategoryForm :data="data" />
        <Variants />
    </PageLayout>
</template>

<script setup>
import { ref, computed } from "vue";
import Variants from "./Variants.vue";
import { useRoute } from "vue-router";
import CategoryForm from "./CategoryForm.vue";
import { apiQuery } from "@lib/helpers";
import PageLayout from "@layouts/admin/PageLayout.vue";
import { useI18n } from 'vue-i18n';

const { t } = useI18n();
const route = useRoute();
const id = computed(() => route.params.id);
const slug = route.params.slug;
const { data, error, isLoading } = apiQuery("categories").useGetById(id);
const links = computed(() => [
    {
        title: t("admin.links.home"),
        link: "/",
    },
    {
        title: t("admin.links.categories"),
        link: "/categories",
    },
    {
        title: slug,
        current: true,
        link: "",
    },
]);
</script>

<style lang="scss" scoped></style>
