<template>
  <Layout
    :errors="errors"
    :title="trans(`models.${model.meta.resourceType}.content.show.action`)"
    :toasts="toasts"
    :resource="model"
  >
    <template #top>
      <resource-action-header :model="model" :action="'show'" />
      <slot name="top" />
    </template>
    <div class="row">
      <div
        v-for="(groups, section) in model.presentation.sections"
        :key="section"
        :class="'px-2 w-' + (section === 'core_data' || section === 'meta_data' ? '3/6' : 'full')"
      >
        <div v-for="(columns, group) in groups" :key="group" :class="Object.keys(columns).length > 0 ? '' : 'hidden'">
          <element-collection
            v-if="section !== 'has_many_relation_data' && section !== 'media_data'"
            :columns="columns"
            :field-group="group"
            :model="model"
          />
          <div v-if="section === 'has_many_relation_data'">
            <card
              v-for="(relatedKey, name) in columns"
              :key="name"
              :title="trans('models.' + model.meta.resourceType + '.relations.' + name)"
              :options="{ style: { card: 'margin-bottom: 1rem;' } }"
            >
              <resource-collection
                :collection-type="model.data.collections[name].meta.config.component?.type ?? 'list'"
                :foreign-name="name"
                :mode="'component'"
                :models="model.data.collections[name]"
                :name="name"
                :parent-model="model"
                :parent-resource="model.meta.resourceType"
              />
            </card>
          </div>
          <div v-if="section === 'media_data' && model.data.media">
            <template v-for="(media, name) in columns">
              <card
                v-if="model.data.media[name]"
                :key="name"
                :title="trans('models.' + model.meta.resourceType + '.attributes.' + name)"
                :options="{ style: { card: 'margin-bottom: 1rem;' } }"
              >
                <media-collection :media="model.data.media[name]" />
              </card>
            </template>
          </div>
        </div>
      </div>
    </div>
    <template #right>
      <admin-sidebox :resource="model" />
    </template>
  </Layout>
</template>

<script setup>
import {
  Card,
  AdminSidebox,
  MediaCollection,
  ElementCollection,
  ResourceCollection,
  ResourceActionHeader,
} from '@imports';

import Layout from '../../../forgeron/Pages/Layout.vue';

const props = defineProps({
  model: { type: Object, default: () => {} },
  toasts: { type: Object, default: () => {} },
  errors: { type: Object, default: () => {} },
  logEntries: { type: Object, default: () => {} },
});
const emit = defineEmits(['addToast']);
</script>
