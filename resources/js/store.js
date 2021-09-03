import Vue from 'vue';
import Vuex from 'vuex';

Vue.use(Vuex);

const state = {
    results: [],
    standings: [],
    teamPowers: [],
    error: {
        title: '',
        message: '',
        standingStatus: false,
        editStatus: false,
    },
};

const mutations = {
    getStanding(state) {

        axios.get('/api/standings')
            .then((all) => {
                state.standings = all.data;
            })
            .catch((error) => {
                console.log(error)
            })
    },
    getResults(state) {
        axios.get('/api/fixture')
            .then((all) => {
                state.results = all.data;
            })
            .catch((error) => {
                console.log(error)
            })
    },
    playLeague() {
        const data = [];
        state.teamPowers.forEach((value, index) => {
            data.push({
                id: index,
                power: value
            });
        });
        axios.post('/api/play-league', data)
            .then(() => {
                state.error.standingStatus = false;
                this.commit('getResults');
                this.commit('getStanding');
            }).catch((err) => {
            if (err.response.status === 400) {
                state.error.title = err.response.data.title;
                state.error.message = err.response.data.message;
                state.error.standingStatus = true;
            }
        })
    },
    playWeek() {
        const data = [];
        state.teamPowers.forEach((value, index) => {
            data.push({
                id: index,
                power: value
            });
        });
        axios.post('/api/play-week', data)
            .then(() => {
                state.error.standingStatus = false;
                this.commit('getResults');
                this.commit('getStanding');
            })
            .catch((err) => {
                if (err.response.status === 400) {
                    state.error.title = err.response.data.title;
                    state.error.message = err.response.data.message;
                    state.error.standingStatus = true;
                }
            })
    },
    resetFixture() {
        state.teamPowers = [];
        state.error.standingStatus = false;
        state.error.editStatus = false;
        axios.get('/api/reset-league')
            .then(() => {
                this.commit('getResults');
                this.commit('getStanding');
            })
            .catch((error) => {
                console.log(error)
            })
    },
    setPower(func, par) {
        Vue.set(state.teamPowers, par.team_id, Number(par.power));
    },
};

export default new Vuex.Store({
    state,
    mutations
});
