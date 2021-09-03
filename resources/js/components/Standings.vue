<template>
    <div class="card w-100">
        <table class="table table-sm table-striped m-0">
            <thead>
            <tr>
                <th colSpan="7" scope="colgroup">
                    League Table
                </th>
            </tr>
            <tr>
                <th scope="col">Power</th>
                <th scope="col">Club</th>
                <th scope="col">PTS</th>
                <th scope="col">P</th>
                <th scope="col">W</th>
                <th scope="col">D</th>
                <th scope="col">L</th>
                <th scope="col">GF</th>
                <th scope="col">GA</th>
                <th scope="col">GD</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="standing in this.$store.state.standings" :key="standing.id">
                <td>
                    <b-form-input v-bind:value="standing.team.power"
                                  type="number"
                                  required
                                  :min=0
                                  :max=100
                                  v-model=standing.team.power
                                  @change="$store.commit('setPower', {'team_id':standing.team_id, 'power':standing.team.power})"
                                  class="w-25 hide-arrow text-center"></b-form-input>
                </td>
                <td><img :src="standing.team.logo"> {{ standing.team.name }}</td>
                <td>{{ standing.points }}</td>
                <td>{{ standing.played }}</td>
                <td>{{ standing.won }}</td>
                <td>{{ standing.drawn }}</td>
                <td>{{ standing.lost }}</td>
                <td>{{ standing.gf }}</td>
                <td>{{ standing.ga }}</td>
                <td>{{ standing.gd }}</td>
            </tr>
            </tbody>
        </table>
    </div>
</template>

<script>
export default {
    data: function () {
        return {
            standings: [],
        }
    },
    mounted() {
        this.$store.commit('getStanding');
    },

    methods: {
        //
    }
}
</script>
