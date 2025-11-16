// O Abismo Observador - Script para Orquestração do Horror

document.addEventListener('DOMContentLoaded', () => {
    const container = document.getElementById('creepy-background-container');
    if (!container) return;

    const vignette = document.getElementById('background-vignette');

    const assets = {
        video: 'blinking-eye-darkness.mp4',
        images: [
            'creepy-2.jpg',
            'creepy-staring-head.jpg',
            'creepy-t.jpg',
            'hardcore.jpg',
            'ominous.jpg',
            'uncanny-2.jpg',
            'uncanny.jpg'
        ]
    };

    const assetBaseUrl = '/creepy_assets/background_assets/';

    // --- Lógica de Cálculo e Posicionamento ---

    function getMaxApparitions() {
        const width = window.innerWidth;
        if (width <= 768) return 1; // Mobile
        if (width <= 1920) return 2; // Desktop padrão
        return 3; // Telas ultra-wide
    }

    function getRandomPosition(zone) {
        const margin = 0.1; // 10% de margem das bordas
        const xMin = zone[0] / 3 + margin;
        const xMax = (zone[0] + 1) / 3 - margin;
        const yMin = zone[1] / 3 + margin;
        const yMax = (zone[1] + 1) / 3 - margin;

        const x = xMin + Math.random() * (xMax - xMin);
        const y = yMin + Math.random() * (yMax - yMin);

        return {
            left: `${x * 100}%`,
            top: `${y * 100}%`
        };
    }

    function getSmartZone() {
        const zones = [
            // Zonas periféricas (evitando o centro [1,1])
            [0, 0], [1, 0], [2, 0],
            [0, 1],         [2, 1],
            [0, 2], [1, 2], [2, 2]
        ];
        // Em mobile, priorizar topo e fundo
        if (window.innerWidth <= 768) {
            const mobileZones = [[0, 0], [1, 0], [2, 0], [0, 2], [1, 2], [2, 2]];
            return mobileZones[Math.floor(Math.random() * mobileZones.length)];
        }
        return zones[Math.floor(Math.random() * zones.length)];
    }

    function getRandomSize() {
        const scale = window.innerWidth <= 768 ? 0.6 : 1;
        const baseSize = 15 + Math.random() * 15; // Entre 15vw e 30vw
        const finalSize = baseSize * scale;
        return {
            width: `${finalSize}vw`,
            height: 'auto'
        };
    }


    // --- Criação dos Elementos ---

    function createApparition() {
        if (container.children.length > getMaxApparitions() + 2) { // +2 para vinheta e ruído
            return;
        }

        const isVideo = Math.random() < 0.2; // 20% de chance de ser o vídeo
        let element;

        if (isVideo) {
            element = createVideoApparition();
        } else {
            element = createImageApparition();
        }

        const position = getRandomPosition(getSmartZone());
        const size = getRandomSize();

        element.style.left = position.left;
        element.style.top = position.top;
        element.style.width = size.width;
        element.style.height = size.height;
        
        // Z-index aleatório para profundidade
        element.style.zIndex = Math.random() < 0.5 ? -4 : -3;

        container.appendChild(element);
        
        // Ativa a vinheta
        vignette.classList.add('active');

        // Remove o elemento após a animação
        const animationDuration = parseFloat(getComputedStyle(element).animationDuration) * 1000;
        setTimeout(() => {
            element.remove();
            // Desativa a vinheta se não houver mais aparições
            if (container.children.length <= 2) {
                vignette.classList.remove('active');
            }
        }, animationDuration);
    }

    function createImageApparition() {
        const imageName = assets.images[Math.floor(Math.random() * assets.images.length)];
        const img = document.createElement('img');
        img.src = `${assetBaseUrl}${imageName}`;
        img.className = 'apparition image';

        // Imagens claras precisam de tratamento especial
        if (imageName.includes('uncanny') || imageName.includes('hardcore')) {
            img.classList.add('light');
        }
        return img;
    }

    function createVideoApparition() {
        const div = document.createElement('div');
        div.className = 'apparition video-container';

        const video = document.createElement('video');
        video.src = `${assetBaseUrl}${assets.video}`;
        video.autoplay = true;
        video.muted = true;
        video.loop = true;
        video.playsInline = true; // Essencial para mobile

        div.appendChild(video);
        return div;
    }

    // --- Loop Principal ---

    function horrorLoop() {
        const nextApparitionIn = 5000 + Math.random() * 10000; // Entre 5 e 15 segundos

        setTimeout(() => {
            createApparition();
            horrorLoop();
        }, nextApparitionIn);
    }

    // Inicia o ciclo de horror
    horrorLoop();
});
