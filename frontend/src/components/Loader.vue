<template>
    <!-- Loader Condicional -->
    <div class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
      <div class="loader">
        <div class="box box-1">
          <div class="side-left"></div>
          <div class="side-right"></div>
          <div class="side-top"></div>
        </div>
        <div class="box box-2">
          <div class="side-left"></div>
          <div class="side-right"></div>
          <div class="side-top"></div>
        </div>
        <div class="box box-3">
          <div class="side-left"></div>
          <div class="side-right"></div>
          <div class="side-top"></div>
        </div>
        <div class="box box-4">
          <div class="side-left"></div>
          <div class="side-right"></div>
          <div class="side-top"></div>
        </div>
      </div>
    </div>
  </template>
  
  <script>
  export default {
    name: 'Loader',
  };
  </script>
  
  <style scoped>
    .loader {
        scale: 3;
        height: 50px;
        width: 40px;
    }
    
    .box {
        position: relative;
        opacity: 0;
        left: 10px;
    }
    
    .side-left {
        position: absolute;
        background-color: #FFCC00; /* Alterado para amarelo alaranjado */
        width: 19px;
        height: 5px;
        transform: skew(0deg, -25deg);
        top: 14px;
        left: 10px;
    }
    
    .side-right {
        position: absolute;
        background-color: #FFD700; /* Alterado para amarelo dourado */
        width: 19px;
        height: 5px;
        transform: skew(0deg, 25deg);
        top: 14px;
        left: -9px;
    }
    
    .side-top {
        position: absolute;
        background-color: #FFFF00; /* Alterado para amarelo puro */
        width: 20px;
        height: 20px;
        rotate: 45deg;
        transform: skew(-20deg, -20deg);
    }  
    
    /* Animação */
    .box-1 {
        animation: from-left 4s infinite;
    }
    
    .box-2 {
        animation: from-right 4s infinite;
        animation-delay: 1s;
    }
    
    .box-3 {
        animation: from-left 4s infinite;
        animation-delay: 2s;
    }
    
    .box-4 {
        animation: from-right 4s infinite;
        animation-delay: 3s;
    }
    
    @keyframes from-left {
        0% {
        z-index: 20;
        opacity: 0;
        translate: -20px -6px;
        }
    
        20% {
        z-index: 10;
        opacity: 1;
        translate: 0px 0px;
        }
    
        40% {
        z-index: 9;
        translate: 0px 4px;
        }
    
        60% {
        z-index: 8;
        translate: 0px 8px;
        }
    
        80% {
        z-index: 7;
        opacity: 1;
        translate: 0px 12px;
        }
    
        100% {
        z-index: 5;
        translate: 0px 30px;
        opacity: 0;
        }
    }
    
    @keyframes from-right {
        0% {
        z-index: 20;
        opacity: 0;
        translate: 20px -6px;
        }
    
        20% {
        z-index: 10;
        opacity: 1;
        translate: 0px 0px;
        }
    
        40% {
        z-index: 9;
        translate: 0px 4px;
        }
    
        60% {
        z-index: 8;
        translate: 0px 8px;
        }
    
        80% {
        z-index: 7;
        opacity: 1;
        translate: 0px 12px;
        }
    
        100% {
        z-index: 5;
        translate: 0px 30px;
        opacity: 0;
        }
    }
  </style>  