<template>
    <div id="map-test" class="h-screen w-full"></div>
  </template>
  
  <script setup>
  import 'leaflet/dist/leaflet.css';
  import L from "leaflet";
  import { onMounted, ref } from "vue";
  
  onMounted(() => {
    // Inicializar o mapa fora do modal, para garantir que ele funciona corretamente
    const mapInstance = L.map('map-test', {
      center: [-29.447402, -52.002427], // Utilize as coordenadas do seu projeto como teste
      zoom: 13,
    });
  
    // Adiciona camada de tiles do OpenStreetMap
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
      attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
    }).addTo(mapInstance);
  
    // Adiciona um marcador
    L.marker([-29.447402, -52.002427])
      .addTo(mapInstance)
      .bindPopup('Teste do mapa aqui')
      .openPopup();
  });
  </script>
  
  <style scoped>
    #map-test {
      height: 100vh;
      width: 100vw;
    }
  </style>  