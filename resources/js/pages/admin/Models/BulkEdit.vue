<template>
  <Layout
    :errors="errors"
    :title="trans(`models.${resourceType}.content.edit.action`)"
    :toasts="toasts"
    :resource="model"
  >
    <div v-if="model.data" class="card rounded-border shadow-md!">
      <div class="card-body">
        <h5 class="card-title text-primary font-semibold text-uppercase">
          {{ trans(`models.${resourceType}.content.edit.action`) }}
        </h5>
        <p class="card-text">
          {{ trans(`models.${resourceType}.content.edit.description`) }}
        </p>
        <resource-form
          :options="{
            actionTitle: 'Änderungen an mehreren ' + trans(`models.${resourceType}.name.plural`) + ' durchführen',
          }"
          :errors="errors"
          :resource="model"
          @updateData="refreshPendingChanges"
        />
      </div>
    </div>
    <template #right>
      <card title="Durchzuführende Änderungen">
        <div v-for="(value, field) in pendingChanges" :key="field">
          <div v-if="pendingChanges[field] && !model.meta.belongsTo[field]" class="flex flex-wrap">
            <div class="w-1/4">
              {{
                trans(
                  `models.${resourceType}.` +
                    (pendingChanges[field + '_id'] !== null ? 'attributes' : 'relations') +
                    `.${field}`
                )
              }}
            </div>
            <div class="w-1/6 px-0">??? =></div>
            <div class="w-2/3 ps-1">{{ pendingChanges[field + '_id'] ?? pendingChanges[field] }}</div>
          </div>
        </div>
      </card>
    </template>
  </Layout>
</template>

<script setup>
import { ref, Card, useResource } from '@imports';
import ResourceForm from '../../../forgeron/components/Resources/ResourceForm.vue';
import Layout from '../../../forgeron/Pages/Layout.vue';

const props = defineProps({
  model: { type: Object, default: () => {} },
  toasts: { type: Object, default: () => {} },
  errors: { type: Object, default: () => {} },
  logEntries: { type: Object, default: () => {} },
  aspectMenuItems: { type: Object, default: () => {} },
});
const { resourceType } = useResource(props, props.model);
const pendingChanges = ref({});
const refreshPendingChanges = (data) => {
  pendingChanges.value = data;
};
</script>
