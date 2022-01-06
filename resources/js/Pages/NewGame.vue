<template>
    <app-layout title="Dashboard">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                New Game
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-3">
                    <h1 class="text-indigo-700 font-bold mb-4">New Game</h1>
                    <form @submit.prevent="submit">
                        <div>
                            <jet-label for="label" value="Game name" />
                            <jet-input id="label" type="text" class="mt-1 block w-full" v-model="form.label" required autofocus />
                        </div>

                        <div class="mt-4">
                            <jet-label for="description" value="Description" />
                            <jet-textarea id="description" class="mt-1 block w-full" v-model="form.description" required />
                        </div>

                        <div>
                            <jet-label for="max_players" value="Max players" />
                            <jet-input id="max_players" type="number" class="mt-1 block w-full" v-model="form.max_players" step="1" required autofocus />
                        </div>

                        <jet-button class="mt-4" :class="{ 'opacity-25': form.processing }" :disabled="form.processing">
                            Create
                        </jet-button>
                    </form>
                </div>
            </div>
        </div>
    </app-layout>
</template>

<script>
    import { defineComponent } from 'vue'
    import AppLayout from '@/Layouts/AppLayout.vue'
    import Welcome from '@/Jetstream/Welcome.vue'
    import JetInput from '@/Jetstream/Input.vue'
    import JetTextarea from '@/Jetstream/Textarea.vue'
    import JetButton from '@/Jetstream/Button.vue'
    import JetLabel from '@/Jetstream/Label.vue'
    import moment from 'moment-timezone';

    export default defineComponent({
        components: {
            AppLayout,
            Welcome,
            JetInput,
            JetTextarea,
            JetButton,
            JetLabel,
        },

        props: ['user'],

        data() {
            return {
                form: this.$inertia.form({
                    label: '',
                    description: '',
                    max_players: 6,
                })
            }
        },

        methods: {
            submit() {
                this.form.post(this.route('newGame'), {
                    onFinish: () => this.$inertia.visit(route('dashboard'))
                })
            }
        },
    })
</script>
