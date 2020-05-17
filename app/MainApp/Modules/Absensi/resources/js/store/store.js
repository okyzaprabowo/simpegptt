const state = {
    example_data: 'exmple data'
};

const getters = {};

const mutations = {
    changeData (state, data) {
      state.example_data = data
    }
};

const actions = {
    updateData({commit}, data) {
        commit('changeData', data)
    }
};

const exampleStore = {
    namespaced: true,
    state,
    mutations,
    actions,
    getters
}

export default {exampleStore};