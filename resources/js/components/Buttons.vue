<template>
    <div>
        <div class="text-center alert alert-danger" v-if="this.$store.state.error.standingStatus === true">
            <strong>{{ this.$store.state.error.title }}</strong><br>
            {{ this.$store.state.error.message }}
        </div>
        <div class="card flex-row w-100 px-3 py-2" style="justify-content:space-between">
            <b-button
                type="button"
                @click="playAll">
                Play All
            </b-button>
            <b-button
                type="button"
                @click="resetFixture">
                Reset Fixture
            </b-button>
            <b-button
                type="button"
                @click="playOne">
                Next Week
            </b-button>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            //
        }
    },
    mounted() {
        //
    },

    methods: {
        playAll() {
            this.$store.commit('playLeague');
        },
        playOne() {
            this.$store.commit('playWeek');
        },
        resetFixture() {
            this.$store.commit('resetFixture')
        }
    }
}
</script>
