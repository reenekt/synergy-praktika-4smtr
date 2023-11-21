<script setup>
import { onMounted, ref } from 'vue';

defineProps({
    modelValue: {
        type: [String, Number, Object, Array],
        required: true,
    },
    multiple: {
        type: Boolean,
        required: false,
        default: false
    },
    required: {
        type: Boolean,
        required: false,
        default: false
    },
    options: {
        type: Array,
        required: true,
    },
    optionsValuePropKey: {
        type: String,
        required: false,
        default: 'value',
    },
    optionsTitlePropKey: {
        type: String,
        required: false,
        default: 'title',
    },
});

defineEmits(['update:modelValue']);

const select = ref(null);

onMounted(() => {
    if (select.value.hasAttribute('autofocus')) {
        select.value.focus();
    }
});

defineExpose({ focus: () => select.value.focus() });
</script>

<template>
    <select
        class="border-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm"
        ref="select"
        :multiple="multiple"
        :value="modelValue"
        :required="required"
        @change="$emit('update:modelValue', $event.target.value)"
    >
        <option
            v-for="(option, i) in options"
            :key="option.value"
            :value="option[optionsValuePropKey]"
        >
            {{ option[optionsTitlePropKey] }}
        </option>
    </select>
</template>
