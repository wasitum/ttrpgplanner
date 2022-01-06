<template>
    <datepicker
        class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm"
        v-model="dateValue"
        ref="input"
    />
</template>

<script>
    import { defineComponent } from 'vue'
    import Datepicker from 'vue3-datepicker'

    export default defineComponent({
        props: ['modelValue'],

        components: { Datepicker },

        data() {
          return {
            dateValue: null,
          };
        },

        emits: ['update:modelValue'],

        methods: {
            focus() {
                this.$refs.input.focus()
            }
        },

        watch: {
            dateValue(e) {
                this.$emit('update:modelValue', e.target.value)
            }
        }
    })
</script>
