<template>
  <Layout
    :errors="errors"
    :title="trans(`models.${resourceType}.content.index.title`)"
    :toasts="toasts"
    :resource="models"
  >
    <template #top>
      <resource-action-header
        :model="getMetaDummy()"
        :action="'index'"
        :title="trans(`models.${resourceType}.content.index.title`)"
      >
        <div class="flex items-center w-3/6 px-0 align-self-center">
          <search-bar
            :name="'product_names'"
            :subject="null"
            :label="trans('common.BASIC_SEARCH_LABEL')"
            :resourceType="resourceType"
            class="placeholder-gray-400 search-bar-style"
            :inputClass="'bg-white border-color-light-gray text-gray-700'"
          />
        </div>
        <div class="flex items-center gap-2 px-0 grow-0 align-self-center">
          <div class="relative z-10" ref="filter">
            <button
              ref="filterButton"
              class="primary-color bg-white border border-color-light-gray focus:outline-none focus:ring-4 focus:ring-gray-200 font-medium rounded-border-half text-sm! px-3 py-2 items-center gap-2 flex cursor-pointer"
              @click="toggleModal(!cachedModals['filter'], 'filter')"
              aria-haspopup="dialog"
            >
              <Icon name="SlidersHorizontal" size="18" fill="none" role="presentation" />
              Filter
            </button>
            <transition name="fade">
              <div
                v-if="cachedModals['filter']"
                ref="modal"
                role="dialog"
                aria-modal="true"
                aria-label="Filter options"
                @keydown.esc="toggleModal(false, 'filter')"
                :style="modalStyle"
                class="fixed shadow-lg bg-gray-100 rounded-border p-2 overflow-hidden overflow-y-auto! max-w-max"
              >
                <div
                  v-for="(attributeType, attribute) in models.meta.listed"
                  :key="attribute"
                  class="flex flex-col gap-4 my-2 align-middle"
                  :class="'forgeron-attribute-header-' + attribute"
                >
                  <column-header
                    :column="attribute"
                    :config="config"
                    :options="{
                      title_key: 'name',
                      sub_title_key: null,
                      titleAttribute: null,
                      targetAttribute: null,
                      imageAttribute: null,
                      flex: 'col',
                    }"
                    :params="getIndexParams(attribute)"
                    :plain="!config.adjustments.bar"
                    :resource="models"
                  />
                </div>
                <div class="flex justify-between w-full gap-2 mt-4">
                  <button
                    class="primary-color bg-white border border-color-light-gray focus:outline-none focus:ring-4 focus:ring-gray-200 font-medium rounded-border-half text-sm! px-3 py-2 items-center gap-2 flex cursor-pointer"
                    @click="handleClear()"
                  >
                    {{ trans('common.configurator.CLEAR') }}
                  </button>
                  <button
                    ref="saveButton"
                    class="primary-color bg-white border border-color-light-gray focus:outline-none focus:ring-4 focus:ring-gray-200 font-medium rounded-border-half text-sm! px-3 py-2 items-center gap-2 flex cursor-pointer"
                    @click="handleSave()"
                  >
                    {{ trans('common.configurator.SAVE') }}
                  </button>
                </div>
              </div>
            </transition>
          </div>
          <component :is="customActionBox(aspect)" :data="customActionData" :resource-type="resourceType" />
        </div>
      </resource-action-header>
    </template>
    <resource-collection
      :collection-type="models.meta.config.component?.type ?? 'list'"
      :mode="'full'"
      :models="models"
      :pagination="false"
      @add-toast="addToast"
    />
    <template #footer-center>
        <pagination v-if="models.page" :name="trans(`models.${resourceType}.name.plural`)" :resource="models" :key="renderIterator" />
    </template>
  </Layout>
</template>

<script setup>
import {
  ref,
  Icon,
  watch,
  router,
  toRefs,
  computed,
  useToasts,
  SearchBar,
  useResource,
  ColumnHeader,
  useResourceParams,
  ResourceCollection,
  useCustomActionBox,
  ResourceActionHeader,
} from '@imports';
import Layout from '../../../forgeron/Pages/Layout.vue';
import Pagination from '../../../forgeron/components/Resources/Navigation/Pagination.vue';
import { onClickOutside, useElementBounding } from '@vueuse/core';
import { toggleModal, cachedModals } from '@/data/default';
const props = defineProps({
  models: { type: Object, default: () => ({}) },
  toasts: { type: Object, default: () => ({}) },
  errors: { type: Object, default: () => ({}) },
  customActionData: { type: Object, default: () => ({}) },
});
const { models, toasts, errors, customActionData } = toRefs(props);
const filter = ref(false);
const filterButton = ref(null);
const { addToast } = useToasts(props);
const { customActionBox } = useCustomActionBox(props);
onClickOutside(filter, () => toggleModal(false, 'filter'));
const { resourceType, meta, config } = useResource(props, models.value);
const renderIterator = ref(0);
const { getIndexParams, paramsWithoutFilterClear } = useResourceParams(props);
const { bottom, left } = useElementBounding(filterButton);

const modalStyle = computed(() => ({
  top: `${bottom.value + 10}px`,
  left: `${left.value - 150}px`,
}));

const handleClear = () => {
  const params = JSON.parse(sessionStorage.getItem('params')) || {};
  if (!params.column) return;
  toggleModal(false, 'filter');
  const url = params.url ?? window.location.href;
  router.get(url, paramsWithoutFilterClear());
};
const handleSave = async () => {
  const params = JSON.parse(sessionStorage.getItem('params')) || {};
  if (!params.column) return;
  await toggleModal(false, 'filter');
  const url = params.url ?? window.location.href;
  router.get(url, params, {
    preserveState: false,
    replace: true,
  });
};

const getMetaDummy = () => {
  return {
    data: [],
    meta: models.value.meta,
  };
};

watch(
  () => props.models.page,
  () => {
    renderIterator.value++;
  }
);
</script>
