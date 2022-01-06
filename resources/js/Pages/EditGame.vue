<template>
    <app-layout title="Dashboard">
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ game.label }}
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-3 mb-3">
                    <h1 class="text-indigo-700 font-bold mb-4">{{ game.label }}</h1>
                    <form v-if="game.user_id === user.id" @submit.prevent="submit">
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
                            Update
                        </jet-button>
                    </form>
                    <template v-else>
                        <div class="text-indigo-500 text-2xl">{{ game.description }}</div>
                        <div class="mt-4">{{ game.max_players }} players maximum</div>
                    </template>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-3">
                        <h1 class="text-2xl mb-5">Signed up players ({{ game.players.length }}/{{ game.max_players}})</h1>
                        <div class="mb-4" v-if="!game.players.length">No player signed up at the moment</div>
                        <div class="mb-4" v-else>
                            <div
                                v-for="player in game.players"
                                :key="`game-players-${player.id}`"
                                class="font-bold mb-3"
                            >
                                {{ player.name }}
                            </div>
                        </div>
                        <button
                            v-if="(user.id !== game.user.id) && isNotInGame"
                            class="text-white font-bold py-2 px-4 rounded"
                            :class="partyFull ? 'bg-gray-300 hover:bg-gray-500' : 'bg-green-500 hover:bg-green-700'"
                            @click="initiateSignup"
                        >
                            <i class="fas mr-3" :class="partyFull ? 'fa-times' : 'fa-user-plus'"></i>{{ partyFull ? 'Party is full' : 'Sign up' }}
                        </button>
                        <button
                            v-else-if="(user.id !== game.user.id) && !isNotInGame"
                            class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded"
                            @click="initiateSignout"
                        >
                            <i class="fas fa-user-times mr-3"></i>Remove myself
                        </button>
                    </div>
                    <div class="bg-white shadow-xl sm:rounded-lg p-3">
                        <template v-if="!game.date">
                            <h1 class="text-2xl mb-5">Possible dates</h1>
                            <div class="mb-4" v-if="!game.possibledates.length">No date has been suggested yet</div>
                            <div class="mb-4 flex flex-wrap justify-start" v-else>
                                <DateCard
                                    v-for="date in game.possibledates"
                                    :key="`game-${game.id}-date-${date.id}`"
                                    :user="user"
                                    :date="date"
                                    :game="game"
                                />
                            </div>
                            <button
                                class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded"
                                @click="initiateDateSuggestion"
                                v-if="!showDateForm"
                            >
                                <i class="fas fa-user-plus mr-3"></i>Suggest a{{ game.possibledates.length ? 'n other' : '' }} date
                            </button>
                            <div v-else>
                                <div class="grid grid-cols-2 gap-4">
                                    <div class="mt-4">
                                        <jet-label for="date" value="Date" />
                                        <datepicker
                                            class="w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm"
                                            id="date"
                                            v-model="dateForm.date"
                                            :lowerLimit="new Date()"
                                            inputFormat="d LLL yyyy"
                                        />
                                    </div>
                                    <div class="mt-4">
                                        <jet-label for="time" value="Time" />
                                        <datepicker
                                            class="w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm"
                                            id="time"
                                            v-model="dateForm.time"
                                            startingView="time"
                                            minimumView="time"
                                            inputFormat="HH:mm"
                                        />
                                    </div>
                                </div>
                                <div class="mt-4">
                                    <jet-label for="timezone" value="Timezone" />
                                    <select
                                        id="timezone"
                                        class="w-full border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm"
                                        v-model="dateForm.timezone"
                                    >
                                        <option
                                            v-for="(timezone, index) in timezones"
                                            :key="`register-timezone-${index}`"
                                            :value="timezone"
                                        >{{ timezone }}</option>
                                    </select>
                                </div>
                                <div class="flex justify-between items-center mt-4">
                                    <button
                                        class="bg-gray-300 hover:bg-gray-500 text-white font-bold py-2 px-4 rounded"
                                        @click="cancelDateForm"
                                    >
                                        Cancel
                                    </button>
                                    <button
                                        class="text-white font-bold py-2 px-4 rounded"
                                        :class="{
                                            'bg-gray-300 hover:bg-gray-500': !this.dateForm.date || !this.dateForm.time || !this.dateForm.timezone,
                                            'bg-green-500 hover:bg-green-700': this.dateForm.date && this.dateForm.time && this.dateForm.timezone,
                                        }"
                                        @click="suggestDate"
                                    >
                                        Confirm
                                    </button>
                                </div>
                            </div>
                        </template>
                        <div v-else class="text-2xl text-green-700 uppercase">
                            Chosen date : <br />{{ formattedGameDate }}
                            <div v-if="game.user_id === user.id" class="mt-4">
                                <button
                                    class="text-base bg-red-500 hover:bg-red-700 text-white font-bold py-1 px-2 rounded"
                                    @click="initiateCancelDate"
                                >
                                    Cancel that date
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <jet-dialog-modal :show="showSignupModal" @close="showSignupModal = false">
            <template #title>
                Sign up for this game
            </template>
            <template #content>
                Do you really want to sign up for this game ?
            </template>
            <template #footer>
                <jet-button type="button" @click.native="showSignupModal = false">
                    Cancel
                </jet-button>
                <jet-button type="button" class="ml-2" @click="signupToGame">
                    Yes, I'm in !
                </jet-button>
            </template>
        </jet-dialog-modal>
        <jet-dialog-modal :show="showSignoutModal" @close="showSignoutModal = false">
            <template #title>
                Sign out of that game
            </template>
            <template #content>
                Do you really want to sign out of that game ?
            </template>
            <template #footer>
                <jet-button type="button" @click.native="showSignoutModal = false">
                    Cancel
                </jet-button>
                <jet-button type="button" class="ml-2" @click="signoutToGame">
                    Yes, I'm out
                </jet-button>
            </template>
        </jet-dialog-modal>
        <jet-dialog-modal :show="showCancelDateModal" @close="showCancelDateModal = false">
            <template #title>
                Cancel date
            </template>
            <template #content>
                Do you really want to cancel the chosen date ?
            </template>
            <template #footer>
                <jet-button type="button" @click.native="showCancelDateModal = false">
                    Cancel
                </jet-button>
                <jet-button type="button" class="ml-2" @click="cancelDate">
                    Yes, let's reschedule
                </jet-button>
            </template>
        </jet-dialog-modal>
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
    import JetDatepicker from '@/Jetstream/Datepicker.vue'
    import JetDialogModal from '@/Jetstream/DialogModal.vue'
    import DateCard from '@/Jetstream/DateCard.vue'
    import moment from 'moment-timezone';
    import { useToast } from "vue-toastification";
    const toast = useToast();
    import Datepicker from 'vue3-datepicker'

    export default defineComponent({
        components: {
            AppLayout,
            Welcome,
            JetInput,
            JetTextarea,
            JetButton,
            JetLabel,
            JetDialogModal,
            JetDatepicker,
            Datepicker,
            DateCard,
        },

        props: ['game', 'user'],

        data() {
            return {
                showSignupModal: false,
                showSignoutModal: false,
                showDateForm: false,
                showCancelDateModal: false,
                form: this.$inertia.form({
                    label: this.game.label,
                    description: this.game.description,
                    max_players: this.game.max_players,
                    date: this.game.date,
                }),
                dateForm: this.$inertia.form({
                    date: null,
                    time: null,
                    timezone: this.user.timezone,
                })
            }
        },

        computed: {
            isNotInGame() {
                const player_ids = this.game.players.map(player => player.id);
                return !player_ids.includes(this.user.id);
            },
            timezones() {
                return moment.tz.names();
            },
            formattedGameDate() {
                if (!this.game.date) {
                    return '';
                }
                const date = moment.utc(this.game.date);
                return date.tz(this.user.timezone).format('DD MMM YYYY, HH:mm');
            },
            partyFull() {
                return this.game.players.length >= this.game.max_players;
            },
        },

        methods: {
            submit() {
                this.form.post(this.route('editGame', {game: this.game.id}), {
                    onFinish: () => {
                        toast.success("Game updated", {
                            timeout: 3000
                        });
                    }
                })
            },
            confirmDate(date) {
                this.form.date = date;
                this.form.post(this.route('editGame'), {
                    // onFinish: () => this.$inertia.visit(route('dashboard'))
                })
            },
            initiateSignup() {
                if ((this.game.user_id !== this.user.id) && this.isNotInGame && !this.showSignupModal && !this.partyFull) {
                    this.showSignupModal = true;
                }
            },
            signupToGame() {
                this.form.post(this.route('signupGame', {game: this.game.id}), {
                   onFinish: () => {
                       toast.success("You are now in !");
                       this.showSignupModal = false;
                   }
                });
            },
            initiateSignout() {
                if ((this.game.user_id !== this.user.id) && !this.isNotInGame && !this.showSignoutModal) {
                    this.showSignoutModal = true;
                }
            },
            signoutToGame() {
                this.form.post(this.route('signoutGame', {game: this.game.id}), {
                    onFinish: () => {
                        toast.success("You are now out :(");
                        this.showSignoutModal = false;
                    }
                });
            },
            initiateDateSuggestion() {
                this.showDateForm = true;
            },
            cancelDateForm() {
                this.showDateForm = false;
                this.dateForm.date = null;
                this.dateForm.time = null;
            },
            suggestDate() {
                if (this.dateForm.date && this.dateForm.time && this.dateForm.timezone) {
                    this.showDateForm = false;
                    const day = moment(this.dateForm.date).format('YYYY-MM-DD');
                    const time = moment(this.dateForm.time).format('HH:mm:ss');
                    const justDate = `${day}T${time}`;
                    const { timezone } = this.dateForm;
                    this.dateForm.date = moment.tz(justDate, timezone).utc().format('YYYY-MM-DD HH:mm:ss');
                    this.dateForm.post(this.route('addDate', {game: this.game.id}), {
                        onFinish: () => {
                            toast.success("Date added", {
                                timeout: 3000
                            });
                            this.dateForm.date = null;
                            this.dateForm.time = null;
                        }
                    })
                }
            },
            initiateCancelDate() {
                if ((this.game.user_id === this.user.id) && this.game.date && !this.showCancelDateModal) {
                    this.showCancelDateModal = true;
                }
            },
            cancelDate() {
                this.form.date = null;
                this.form.post(this.route('editGame', {game: this.game.id}), {
                    onFinish: () => {
                        toast.success("Date cancelled", {
                            timeout: 3000
                        });
                        this.showCancelDateModal = false;
                    }
                })
            },
        },
    })
</script>
