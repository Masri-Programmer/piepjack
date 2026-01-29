<template>
  <Layout
    :errors="errors"
    :title="trans(`models.${resourceType}.content.edit.action`)"
    :toasts="toasts"
    :resource="model"
  >
    <template #top>
      <resource-action-header :model="model" :action="'edit'" />
    </template>
    <ul class="gap-1 nav nav-tabs">
      <li class="nav-item">
        <button :class="focus === 'self' ? 'active' : 'text-gray-600!'" class="nav-link" @click="setFocus('self')">
          {{ trans(`models.${resourceType}.name.singular`) }}
        </button>
      </li>
      <template v-if="model.data.id">
        <template v-for="(collection, name) in model.data.collections" :key="name">
          <li v-if="meta.editable.includes(name)" class="nav-item">
            <button :class="focus === name ? 'active' : 'text-gray-600!'" class="nav-link" @click="setFocus(name)">
              {{ trans(`models.${resourceType}.relations.${name}`) }}
            </button>
          </li>
        </template>
      </template>
    </ul>
    <div v-if="model.data && focus === 'self'" class="card rounded-border">
      <div class="card-body">
        <resource-form :errors="errors" :resource="model" @update:toasts="addToasts($event)" />
      </div>
    </div>
    <div v-if="model.data.id">
      <div v-for="(collection, name) in model.data.collections" :key="name">
        <template v-if="meta.editable.includes(name) && focus == name">
          <card
            :id="`resource-component_${resourceType}-${name}_list`"
            :title="trans('models.' + resourceType + '.relations.' + name)"
            class="relative"
          >
            <resource-collection
              :collection-type="model.data.collections[name].meta.config.component?.type ?? 'list'"
              :mode="'dashboard-component'"
              :models="model.data.collections[name]"
              :name="name"
              :options="getOptions(name)"
              @detach="(detached) => detach(name, detached)"
              @updatePicked="(picked) => updatePicked(picked, name)"
              @update:data="(data) => $emit('update:data', data)"
              @update:relation="(data) => $emit('update:relation', data)"
              @trigger:refresh="reload()"
            />
          </card>
          <div v-if="model.meta.belongsToMany[name]" class="flex flex-wrap">
            <div class="w-full">
              <card
                :id="`resource-component_${resourceType}-${name}_selection`"
                :title="trans('models.' + resourceType + '.relations.' + name) + ' hinzufügen'"
                class="mb-2"
              >
                <resource-selection
                  :name="name"
                  :resource-type="model.meta.collections[name]"
                  :origin="model"
                  @update:attached="(attachId) => attach(name, attachId)"
                ></resource-selection>
              </card>
            </div>
          </div>
          <div v-else-if="model.meta.hasMany[name]" class="flex flex-wrap">
            <div class="w-full">
              <picked-resource-card :name="name" :picked="picked[name]" :resource="model"></picked-resource-card>
            </div>
          </div>
        </template>
      </div>
    </div>
    <template #right>
      <admin-sidebox :resource="model" />
    </template>
  </Layout>
</template>

<script setup>
import {
  ref,
  Card,
  router,
  useModels,
  useToasts,
  useResource,
  AdminSidebox,
  ResourceSelection,
  ResourceCollection,
  PickedResourceCard,
  ResourceActionHeader,
} from '@imports';
import ResourceForm from '../../../forgeron/components/Resources/ResourceForm.vue';
import Layout from '../../../forgeron/Pages/Layout.vue';

const props = defineProps({
  aspectMenuItems: { type: Object, default: () => {} },
  model: { type: Object, default: () => {} },
  logEntries: { type: Object, default: () => {} },
  toasts: { type: Object, default: () => {} },
  errors: { type: Object, default: () => {} },
});
const emit = defineEmits(['update:toast']);
const { meta, resourceType } = useResource(props, props.model);
const picked = ref(props.model.data?.picked);
const { addToasts } = useToasts(props);
const focus = ref(props.model.meta?.adjustments.focus ?? 'self');

const updatePicked = (pickedModel, name) => {
  picked.value[name].data = pickedModel.model.data;
};

const setFocus = (name) => {
  const { focus: setFocusAdjustment } = useModels({ resourceType: props.model.meta.resourceType });
  setFocusAdjustment(props.model.meta.resourceType, name);
  focus.value = name;
};

const attach = async (name, attachId) => {
  const { attachModel } = useModels({ resourceType: props.model.meta.resourceType });
  await attachModel(props.model.data.id, props.model.meta.resourceType, name, attachId);
  router.reload();
};

const detach = async (name, detached) => {
  const { detachModel } = useModels({ resourceType: props.model.meta.resourceType });
  await detachModel(props.model.data.id, props.model.meta.resourceType, name, detached.data.id);
  router.reload();
};

const reload = () => {
  router.reload();
};

const getOptions = (name) => {
  let options = {};
  options.relation = props.model.data.collections[name].meta.relationInfo;
  options.parentResource = props.model;

  return options;
};
</script>
