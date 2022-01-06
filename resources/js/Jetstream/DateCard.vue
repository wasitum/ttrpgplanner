<template>
    <div class="border border-gray-100 shadow-xl sm:rounded-lg p-3 mr-3 mb-3 text-center">
        <div :class="{'text-green-500': dateWorks}">
            <div class="text-xl">{{ day }} {{ month }}</div>
            <div class="font-bold">{{ time }}</div>
        </div>
        <hr class="my-3" />
        <div class="">Joueurs : <span>{{ players.length }}/{{ game.players.length }}</span></div>
        <div class="">Game Master : <i class="fas" :class="gameMasterOk ? 'fa-thumbs-up text-green-500' : 'fa-thumbs-down text-red-500'"></i></div>
        <template v-if="isSignedUp || user.id === game.user_id">
            <hr class="my-3" />
            <button
                class="text-white font-bold py-1 px-2 rounded"
                :class="{
                    'bg-red-300 hover:bg-red-500': isOk,
                    'bg-green-500 hover:bg-green-700': !isOk,
                }"
                @click="toggleAttendance"
            >
                <i class="far" :class="isOk ? 'fa-thumbs-down' : 'fa-thumbs-up'"></i>
            </button>
        </template>
        <template v-if="user.id === game.user_id">
            <hr class="my-3" />
            <button
                class="bg-green-500 hover:bg-green-700 text-white font-bold py-1 px-2 rounded"
                @click="initiateConfirmDate"
            >
                Choose that date
            </button>
        </template>
        <jet-dialog-modal :show="showValidateModal" @close="showValidateModal = false">
            <template #title>
                Confirm this date
            </template>
            <template #content>
                Are you sure this is the final date for the game ?
                <template v-if="!dateWorks">
                    <br /><br />Not everyone confirmed their availability for that date !
                </template>
            </template>
            <template #footer>
                <jet-button type="button" @click.native="showValidateModal = false">
                    Cancel
                </jet-button>
                <jet-button type="button" class="ml-2" @click="confirmDate">
                    Yes, let's play {{ dateWorks ? '' : 'anyway' }} !
                </jet-button>
            </template>
        </jet-dialog-modal>
    </div>
</template>

<script>
import moment from 'moment-timezone';
import { useToast } from "vue-toastification";
import JetDialogModal from '@/Jetstream/DialogModal.vue'
import JetButton from '@/Jetstream/Button.vue'
const toast = useToast();
export default {
    props: {
        user: {
            type: Object,
            required: true,
        },
        date: {
            type: Object,
            required: true,
        },
        game: {
            type: Object,
            required: true,
        },
    },

    components: {
        JetDialogModal,
        JetButton,
    },

    data() {
        return {
            showValidateModal: false,
        };
    },

    computed: {
        day() {
            const date = moment.utc(this.date.date);
            return date.tz(this.user.timezone).format('DD');
        },
        month() {
            const date = moment.utc(this.date.date);
            return date.tz(this.user.timezone).format('MMM');
        },
        time() {
            const date = moment.utc(this.date.date);
            return date.tz(this.user.timezone).format('HH:mm');
        },
        isSignedUp() {
            return this.game.players.map(player => player.id).includes(this.user.id);
        },
        isOk() {
            return this.date.users.map(user => user.id).includes(this.user.id);
        },
        players() {
            return this.date.users.filter(user => user.id !== this.game.user_id);
        },
        gameMasterOk() {
            return this.date.users.map(user => user.id).includes(this.game.user_id);
        },
        dateWorks() {
            return this.gameMasterOk && this.players.length === this.game.players.length;
        },
    },

    methods: {
        toggleAttendance() {
            const form = this.$inertia.form();
            form.post(this.route('toggleDate', {date: this.date.id}), {
                onFinish: () => {
                    toast.success("Your vote has been registered", {
                        timeout: 3000
                    });
                }
            })
        },
        initiateConfirmDate() {
            this.showValidateModal = true;
        },
        confirmDate() {
            const form = this.$inertia.form({
                ...this.game,
                date: this.date.date,
            });
            form.post(this.route('editGame', {game: this.game.id}), {
                onFinish: () => {
                    toast.success("Date confirmed !", {
                        timeout: 3000
                    });
                }
            })
        },
    },
}
</script>
