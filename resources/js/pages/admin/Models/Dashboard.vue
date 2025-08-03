<template>
  <Layout
    :errors="errors"
    :title="'Dashboard: ' + trans(`models.${resourceType}.name.plural`)"
    :toasts="toasts"
    :resource="model"
  >
    <template #top>
      <resource-action-header
        :model="model"
        :action="'dashboard'"
        :title="trans(`models.${resourceType}.name.plural`)"
      />
    </template>
    <ul class="nav nav-tabs gap-1">
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
      <div class="card-body relative">
        <div class="grid gap-1 my-2">
          <h5 class="card-title text-primary font-semibold text-uppercase">
            {{ trans(`models.${resourceType}.content.` + (model.data.id ? 'edit' : 'create') + `.action`) }}
          </h5>
          <p class="card-text">
            {{ trans(`models.${resourceType}.content.` + (model.data.id ? 'edit' : 'create') + `.description`) }}
          </p>
        </div>
        <resource-form :key="model.data.id" :errors="errors" :resource="model" />
      </div>
    </div>

    <div v-if="model.data.id">
      <div v-for="(collection, name) in model.data.collections" :key="name">
        <template v-if="meta.editable.includes(name) && focus == name">
          <relation-collection-card
            :name="name"
            :resource="model"
            @updatePicked="(picked) => updatePicked(picked, name)"
            @detach="(detached) => detach(name, detached)"
            @trigger:refresh="reload()"
          ></relation-collection-card>
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

    <template #footer-main>
      <div v-if="models.page">
        <pagination :name="trans(`models.${resourceType}.name.plural`)" :resource="models" />
      </div>
    </template>
    <template v-if="models?.data" #right>
      <admin-sidebox :resource="model" :resource-collection="models" :focus="'resources'" />
    </template>
  </Layout>
</template>

<script setup>
import {
  ref,
  Card,
  router,
  useToasts,
  useModels,
  Pagination,
  useResource,
  AdminSidebox,
  ResourceSelection,
  useResourceParams,
  useCustomActionBox,
  PickedResourceCard,
  ResourceActionHeader,
  RelationCollectionCard,
} from '@imports';
import ResourceForm from '../../../forgeron/components/Resources/ResourceForm.vue';
import Layout from '../../../forgeron/Pages/Layout.vue';
const props = defineProps({
  model: { type: Object, default: () => {} },
  models: { type: Object, default: () => {} },
  toasts: { type: Object, default: () => {} },
  errors: { type: Object, default: () => {} },
});
const { addToast } = useToasts(props);
const picked = ref(props.model.data.picked);
const { getIndexParams } = useResourceParams(props);
const { customActionBox } = useCustomActionBox(props);
const { meta, resourceType } = useResource(props, props.model);
const focus = ref(props.model.meta.adjustments.focus ?? 'self');

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
</script>
