<template>
  <div>
    <div class="rod" v-for="(rod, index) in hanoi" @click.prevent="clickRod(index)">
      <transition name="fade" v-for="disk in rod">
        <div class="disk" :style="getDiskStyle(disk, index)" :class="{active:activeDisk(disk)}"></div>
      </transition>
    </div>
    <!--<button @click="start()">Переложить</button>-->
  </div>
</template>

<script>
    import {store} from './store';
    export default {
        computed: {
            hanoi: state => store.state.hanoi,
            diskSelected: state => store.state.diskSelected
        },
        data () {
            return {
                heightHanoi: 7,//Выота башни
                startRow: 0,//Номер шпиля с которого начинается башня,
                endRow: 2, //Номер шпиля на который будетм перекладывать
            }
        },
        created(){
            store.dispatch('create', {heightHanoi: this.heightHanoi, startRow: this.startRow});
        },
        methods: {
            getDiskStyle: function (disk, rodIndex) {
                let width = this.getWidth(disk);

                return {
                    'bottom': this.getMarginBottom(disk, rodIndex),
                    'background-color': this.getColor(disk),
                    'width': this.getWidth(disk) + 'px',
                    'margin-left': '-' + (width-16) / 2 + 'px'
                }
            },
            getMarginBottom: function (disk, rodIndex) {
                let indexDisk = this.hanoi[rodIndex].indexOf(disk);
                return indexDisk * 20 + 'px'
            },
            getColor: function (disk) {
                let colorNum = '0.'+disk;
                return '#'+(colorNum*0xFFFFFF<<0).toString(16);
            },
            getWidth: function (disk) {
                let maxWidth = 220;
                let step = 10;
                return maxWidth - (step * disk);
            },
            clickRod: function (destinationRodIndex) {
                if(this.diskSelected && this.diskSelected.rodIndex != destinationRodIndex){
                    if (this.isMovePossible(destinationRodIndex)){
                        store.dispatch('move', destinationRodIndex);
                    }
                } else {
                    store.dispatch('selectDisk', destinationRodIndex);
                }
            },
            activeDisk(disk){
                if (this.diskSelected && this.diskSelected.disk == disk){
                    return true
                } else {
                    return false
                }
            },
            isMovePossible: function (destinationRodIndex) {
                if (!this.diskSelected) {
                    // Не выбран диск, перекладывать нечего — ход запрещён
                    return false;
                }
                if (this.hanoi[destinationRodIndex].length == 0) {
                    return true;
                }
                console.log(this.hanoi[destinationRodIndex][this.hanoi[destinationRodIndex].length-1]);
                return this.diskSelected.disk > this.hanoi[destinationRodIndex][this.hanoi[destinationRodIndex].length-1];
            },
        }
    }
</script>

<style>
  #app {
    position: absolute;
    top: 0;
    left: 0;
    width: 800px;
    height: 500px;
    background-color: lightgray;
  }
  .rod {
    height: 400px;
    margin-left: 110px;
    margin-right: 110px;
    background-color: blueviolet;
    width: 32px;
    display: inline-block;
    margin-top: 100px;
    position: relative;
    cursor: pointer;
  }

  .disk {
    position: absolute;
    height: 20px;
    font-size: 9px;
    color: white;
  }
  .disk.active {
    opacity: .5;
  }
  .fade-enter-active, .fade-leave-active {
    transition: opacity .1s
  }
  .fade-enter, .fade-leave-to /* .fade-leave-active для <2.1.8 */ {
    opacity: 0
  }
</style>
