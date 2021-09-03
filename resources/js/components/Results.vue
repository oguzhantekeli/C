<template>
    <div class="col-md-12 border mb-5">
        <h3 class="text-center">Results</h3>
        <div class="pt-2 pb-2" v-for="(result, index) in this.$store.state.results" v-bind:key="result.id">
            <div class="col-md-12 text-center">
                <h6>Week {{ index }}</h6>
            </div>
            <div class="row">
                <div class="col-md-6" v-for="match in result" v-bind:key="match.id">
                    <div class="result-single">
                        <div class="col r-aligner"
                             v-bind:class="matchResult(match.home_team_goal, match.away_team_goal)">
                            <span>{{ match.home_team.name }}</span>
                            <img :src="match.home_team.logo">
                        </div>
                        <div class="result-button">
                            <b-button
                                @click="passData(match)"
                                v-b-modal="'editMatchModal'">
                                {{ match.home_team_goal === null ? 0 : match.home_team_goal }}
                                -
                                {{ match.away_team_goal === null ? 0 : match.away_team_goal }}
                            </b-button>
                        </div>
                        <div class="col l-aligner"
                             v-bind:class="matchResult(match.away_team_goal, match.home_team_goal)">
                            <img :src="match.away_team.logo">
                            <span>{{ match.away_team.name }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <b-modal
            v-bind:id="'editMatchModal'"
            ref="modal"
            title="Edit Match Score"
            @ok.prevent="handleSubmit()"
            @cancel="$store.state.error.editStatus = false">
            <b-form
                inline ref="form"
                @submit.prevent="handleSubmit">
                <p v-if="errors.length">
                    <b>Please correct the following error(s):</b>
                <ul>
                    <li class="danger" v-for="error in errors" :key="error.id">{{ error }}</li>
                </ul>
                </p>
                <b-form-group>
                    <b-input-group v-bind:prepend="this.selectedMatch.home_team_name"
                                   v-bind:append="this.selectedMatch.away_team_name">
                        <b-form-input
                            v-bind:value="this.selectedMatch.home_team_goal===null ? 0 : this.selectedMatch.home_team_goal"
                            type="number"
                            class="mb-2 mr-sm-2 mb-sm-0 text-center"
                            required
                            :min="0"
                            :max="10"
                            @change="inputHomeChange($event)"
                        ></b-form-input>
                        <span> - </span>
                        <b-form-input
                            v-bind:value="this.selectedMatch.away_team_goal===null ? 0 : this.selectedMatch.away_team_goal"
                            type="number"
                            class="mb-2 ml-sm-2 mb-sm-0 text-center"
                            required
                            :min="0"
                            :max="10"
                            @change="inputAwayChange($event)"
                        ></b-form-input>
                    </b-input-group>
                </b-form-group>
            </b-form>
            <div class="text-center alert alert-danger" v-if="$store.state.error.editStatus === true">
                <strong>{{ $store.state.error.title }}</strong><br>
                {{ $store.state.error.message }}
            </div>
        </b-modal>
    </div>
</template>

<script>
export default {
    data() {
        return {
            results: [],
            submittedScores: [],
            errors: [],
            selectedMatch: {
                id: '',
                home_team_goal: '',
                away_team_goal: '',
                home_team_name: '',
                away_team_name: '',
            },
        }
    },
    mounted() {
        this.$store.commit('getResults');
    },

    methods: {
        inputHomeChange(e) {
            this.selectedMatch.home_team_goal = Number(e);
        },
        inputAwayChange(e) {
            this.selectedMatch.away_team_goal = Number(e);
        },
        passData(result) {
            this.selectedMatch = {
                id: result.id,
                home_team_goal: Number(result.home_team_goal),
                away_team_goal: Number(result.away_team_goal),
                home_team_name: result.home_team.name,
                away_team_name: result.away_team.name,
            }
        },
        handleSubmit() {
            axios.post('/api/update-match', this.selectedMatch)
                .then(() => {
                    this.$store.state.error.editStatus = false;
                    this.$store.commit('getResults');
                    this.$store.commit('getStanding');
                    this.$bvModal.hide('editMatchModal');
                })
                .catch((err) => {
                    if (err.response.status === 400) {
                        this.$store.state.error.title = err.response.data.title;
                        this.$store.state.error.message = err.response.data.message;
                        this.$store.state.error.editStatus = true;
                    }
                })
        },
        matchResult(firstTeam, secondTeam) {
            if (firstTeam != undefined && secondTeam != undefined) {
                if (firstTeam > secondTeam) {
                    return 'winner';
                } else if (secondTeam > firstTeam) {
                    return 'loser';
                } else {
                    return 'drawn';
                }
            }
        }
    }
}
</script>
