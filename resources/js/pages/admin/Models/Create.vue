<template>
  <Layout
    :errors="errors"
    :title="trans(`models.${resourceType}.content.create.action`)"
    :toasts="toasts"
    :resource="model"
  >
    <template #top>
      <resource-action-header :model="model" :action="'create'" />
    </template>
    <Box v-if="model.data">
      <resource-form :errors="errors" :resource="model" :options="{ relationCollections: true }" />
    </Box>
    <template #right>
      <admin-sidebox :resource="model" />
    </template>
  </Layout>
</template>

<script setup>
import { useResource, ResourceActionHeader, Box, AdminSidebox } from '@imports';
import ResourceForm from '../../../forgeron/components/Resources/ResourceForm.vue';
import Layout from '../../../forgeron/Pages/Layout.vue';

const props = defineProps({
  aspectMenuItems: { type: Object, default: () => {} },
  model: { type: Object, default: () => {} },
  toasts: { type: Object, default: () => {} },
  errors: { type: Object, default: () => {} },
});
const { resourceType } = useResource(props, props.model);
</script>
