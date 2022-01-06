<template>
    <app-layout title="Dashboard">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                Dashboard
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-3">
                    <div class="w-full flex justify-end items-center mb-4">
                        <Link :href="route('newGame')" class="bg-green-700 hover:bg-green-900 text-white font-bold py-2 px-4 rounded mr-4">
                            <i class="fas fa-plus mr-3"></i>New game
                        </Link>
                    </div>
                    <table class="table-auto min-w-full leading-normal">
                        <thead>
                        <tr>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Name
                            </th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Description
                            </th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Created by
                            </th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Players
                            </th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider">
                                Date
                            </th>
                            <th class="px-5 py-3 border-b-2 border-gray-200 bg-gray-100 text-left text-xs font-semibold text-gray-600 uppercase tracking-wider"></th>
                        </tr>
                        </thead>
                        <tbody>
                        <tr v-if="!games.length">
                            <td colspan="6" class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                No game yet
                            </td>
                        </tr>
                        <tr
                            v-for="game in games"
                            :key="`dashboard-game-${game.id}`"
                        >
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                {{ game.label }}
                            </td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                {{ game.description }}
                            </td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                {{ game.user.name }}
                            </td>
                            <td
                                class="px-5 py-5 border-b border-gray-200 bg-white text-sm"
                                :class="isGameFull(game) ? 'text-red-500' : 'text-green-700 font-semibold'"
                            >
                                {{ game.players.length }} / {{ game.max_players }}
                            </td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                {{ formatDate(game.date) }}
                            </td>
                            <td class="px-5 py-5 border-b border-gray-200 bg-white text-sm">
                                <div class="flex justify-between items-center">
                                    <Link :href="route('editGame', {game: game.id})" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded mr-4">
                                        <i class="fas fa-eye"></i>
                                    </Link>
                                    <button
                                        v-if="user.id === game.user.id"
                                        class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded"
                                        @click="showDeleteModal = game.id"
                                    >
                                        <i class="far fa-trash-alt"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <jet-dialog-modal :show="showDeleteModal" @close="showDeleteModal = null">
            <template #title>
                Delete this game
            </template>
            <template #content>
                Are you sure you want to delete this game ?
            </template>
            <template #footer>
                <jet-button type="button" @click.native="showDeleteModal = null">
                    Cancel
                </jet-button>
                <jet-button type="button" class="ml-2" @click="deleteGame">
                    Yes, cancel it completely
                </jet-button>
            </template>
        </jet-dialog-modal>
    </app-layout>
</template>

<script>
    import { defineComponent } from 'vue'
    import AppLayout from '@/Layouts/AppLayout.vue'
    import Welcome from '@/Jetstream/Welcome.vue'
    import JetDialogModal from '@/Jetstream/DialogModal.vue'
    import JetButton from '@/Jetstream/Button.vue'
    import moment from 'moment-timezone';
    import { Link } from '@inertiajs/inertia-vue3';
    import { useToast } from "vue-toastification";
    const toast = useToast();

    export default defineComponent({
        components: {
            AppLayout,
            Welcome,
            Link,
            JetDialogModal,
            JetButton,
        },

        props: ['games', 'user'],

        data() {
            return {
                showDeleteModal: null,
            };
        },

        methods: {
            isGameFull(game) {
              return game.players.length >= game.max_players;
            },
            formatDate(date) {
                if (!date) {
                    return 'Not set yet';
                }
                return moment.tz(date, this.user.timezone).format('DD MMM YYYY, HH:mm');
            },
            isNotInGame(game) {
              const player_ids = game.players.map(player => player.id);
              return !player_ids.includes(this.user.id);
            },
            deleteGame() {
                const form = this.$inertia.form();
                form.delete(this.route('deleteGame', {game: this.showDeleteModal}), {
                    onFinish: () => {
                        toast.success("Game deleted", {
                            timeout: 3000
                        });
                        this.showDeleteModal = null;
                    }
                });
            },
        },
    })
</script>
