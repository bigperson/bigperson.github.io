import Vue from 'vue';
import Vuex from 'vuex';
Vue.use(Vuex);

export const store = new Vuex.Store({
    state: {
        hanoi: [[], [], []], //Башня
        diskSelected: null,
    },
    mutations: {
        CREATE: function (state, obj) {
           state.hanoi[obj.index] = obj.hanoi;
        },
        SELECT_DISK: function (state, disk) {
            state.diskSelected = disk;
        },
        MOVE: function (state, destinationRodIndex) {
            let top_disk = state.hanoi[state.diskSelected.rodIndex].pop(); // снимаем диск сверху со стержня departure_rod
            console.log('top disk is', top_disk);
            state.hanoi[destinationRodIndex].push(top_disk); // кладём его сверху на стержень destination_rod
            console.log('result state is', JSON.stringify(state.hanoi));
        }
    },
    actions: {
        create: function (state, obj) {
            let hanoi = [];
            for (let i = 1; i <= obj.heightHanoi; i++) {
                hanoi.push(i)
            }
            store.commit('CREATE', {index:obj.startRow, hanoi: hanoi});
        },
        selectDisk: function (store, rodIndex) {
            if(store.state.diskSelected && store.state.diskSelected.rodIndex == rodIndex){
                store.commit('SELECT_DISK', null);
            } else {
                let disk = store.state.hanoi[rodIndex][store.state.hanoi[rodIndex].length - 1];
                let diskSelected = { rodIndex: rodIndex, disk: disk };
                store.commit('SELECT_DISK', diskSelected);
            }
        },
        move: function (store, destinationRodIndex) {
            store.commit('MOVE', destinationRodIndex);
            store.commit('SELECT_DISK', null);
        }
    }
});
