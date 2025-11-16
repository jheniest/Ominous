// O Abismo Observador - Script para Orquestração do Horror

document.addEventListener('DOMContentLoaded', () => {
    const container = document.getElementById('creepy-background-container');
    if (!container) return;

    const vignette = document.getElementById('background-vignette');
    
    // Histórico de aparições recentes (para evitar duplicatas)
    const recentApparitions = [];
    
    // Zonas ocupadas atualmente (para evitar sobreposição)
    const occupiedZones = new Set();
    
    // Zonas estratégicas ao redor do centro (logo) - mais próximas
    const strategicZones = [
        { x: 0.25, y: 0.15, label: 'top-left' },
        { x: 0.60, y: 0.15, label: 'top-right' },
        { x: 0.15, y: 0.40, label: 'mid-left' },
        { x: 0.70, y: 0.40, label: 'mid-right' },
        { x: 0.25, y: 0.65, label: 'bottom-left' },
        { x: 0.60, y: 0.65, label: 'bottom-right' },
        { x: 0.08, y: 0.25, label: 'far-left' },
        { x: 0.80, y: 0.25, label: 'far-right' }
    ];

    const assets = {
        videos: ['blinking-eye-darkness.mp4', 'video-scene-house.mp4'],
        images: [
            'creepy-2.jpg',
            'creepy-staring-head.jpg',
            'creepy-t.jpg',
            'hardcore.jpg',
            'ominous.jpg',
            'scary-hospital-heads.jpg',
            'uncanny-2.jpg'
        ]
    };

    const assetBaseUrl = '/creepy_assets/background_assets/';

    // --- Lógica de Cálculo e Posicionamento ---

    function getMaxApparitions() {
        const width = window.innerWidth;
        if (width <= 768) return 1; // Mobile
        return 2; // Desktop e ultra-wide
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
            widthVw: finalSize,
            width: `${finalSize}vw`,
            height: 'auto'
        };
    }
    
    function getStrategicPosition() {
        // Filtrar zonas disponíveis
        const availableZones = strategicZones.filter(zone => !occupiedZones.has(zone.label));
        
        if (availableZones.length === 0) {
            // Se todas estão ocupadas, libera uma aleatória
            const randomOccupied = Array.from(occupiedZones)[0];
            occupiedZones.delete(randomOccupied);
            return getStrategicPosition();
        }
        
        // Seleciona zona aleatória das disponíveis
        const zone = availableZones[Math.floor(Math.random() * availableZones.length)];
        occupiedZones.add(zone.label);
        
        // Adiciona pequena variação para não ficar exatamente no mesmo ponto
        const variance = 0.05;
        const x = zone.x + (Math.random() - 0.5) * variance;
        const y = zone.y + (Math.random() - 0.5) * variance;
        
        return {
            left: `${x * 100}%`,
            top: `${y * 100}%`,
            zone: zone.label
        };
    }


    // --- Criação dos Elementos ---

    function createApparition() {
        if (!apparitionsEnabled) {
            return;
        }
        
        if (container.children.length > getMaxApparitions() + 2) { // +2 para vinheta e ruído
            return;
        }

        const isVideo = Math.random() < 0.2; // 20% de chance de ser vídeo
        const size = getRandomSize();
        
        let element, assetName;

        if (isVideo) {
            const result = createVideoApparition();
            element = result.element;
            assetName = result.name;
        } else {
            const result = createImageApparition();
            element = result.element;
            assetName = result.name;
            
            // Evitar duplicatas (exceto vídeo)
            if (recentApparitions.includes(assetName)) {
                // Tenta pegar outra imagem
                const availableImages = assets.images.filter(img => !recentApparitions.includes(img));
                if (availableImages.length > 0) {
                    const newImageName = availableImages[Math.floor(Math.random() * availableImages.length)];
                    const img = document.createElement('img');
                    img.src = `${assetBaseUrl}${newImageName}`;
                    img.className = 'apparition image';
                    if (newImageName.includes('uncanny') || newImageName.includes('hardcore')) {
                        img.classList.add('light');
                    }
                    // Adicionar máscara de borda
                    if (Math.random() < 0.6) {
                        img.classList.add('masked');
                    }
                    element = img;
                    assetName = newImageName;
                }
            }
            
            // Registrar aparição
            recentApparitions.push(assetName);
            if (recentApparitions.length > 3) {
                recentApparitions.shift();
            }
        }

        const position = getStrategicPosition();

        element.style.left = position.left;
        element.style.top = position.top;
        element.style.width = size.width;
        element.style.height = size.height;
        
        // Z-index aleatório para profundidade
        element.style.zIndex = Math.random() < 0.5 ? -4 : -3;
        
        // Armazenar zona para liberação posterior
        element.dataset.zone = position.zone;

        container.appendChild(element);
        
        // Ativa a vinheta
        vignette.classList.add('active');

        // Remove o elemento após a animação
        const animationDuration = parseFloat(getComputedStyle(element).animationDuration) * 1000;
        setTimeout(() => {
            // Libera a zona
            if (element.dataset.zone) {
                occupiedZones.delete(element.dataset.zone);
            }
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

        // Aplicar efeitos específicos baseados na imagem
        if (imageName.includes('uncanny') || imageName.includes('hardcore')) {
            img.classList.add('light');
        }
        
        if (imageName.includes('staring') || imageName.includes('hospital')) {
            img.classList.add('intense-stare');
        }
        
        if (imageName.includes('creepy-2') || imageName.includes('creepy-t')) {
            img.classList.add('distorted');
        }
        
        if (imageName.includes('ominous')) {
            img.classList.add('shadowy');
        }
        
        // 70% das imagens recebem máscara de borda
        if (Math.random() < 0.7) {
            img.classList.add('masked');
        }
        
        return { element: img, name: imageName };
    }

    function createVideoApparition() {
        const videoName = assets.videos[Math.floor(Math.random() * assets.videos.length)];
        const div = document.createElement('div');
        div.className = 'apparition video-container';

        const video = document.createElement('video');
        video.src = `${assetBaseUrl}${videoName}`;
        video.autoplay = true;
        video.muted = true;
        video.loop = true;
        video.playsInline = true; // Essencial para mobile

        div.appendChild(video);
        return { element: div, name: videoName };
    }

    // --- Loop Principal ---

    function horrorLoop() {
        const nextApparitionIn = 4000 + Math.random() * 8000; // Entre 4 e 12 segundos

        setTimeout(() => {
            // Só cria se não exceder o limite
            const currentApparitions = container.children.length - 2;
            if (currentApparitions < getMaxApparitions()) {
                createApparition();
            }
            horrorLoop();
        }, nextApparitionIn);
    }

    // Estado dos controles
    let soundEnabled = true;
    let apparitionsEnabled = true;
    
    // Inicializa áudio ambiente
    const ambientAudio = new Audio('/creepy_assets/sound/solitude_ambient.mp3');
    ambientAudio.loop = true;
    ambientAudio.volume = 0.3;
    
    // Inicia áudio ao primeiro clique/interação (requisito do navegador)
    document.addEventListener('click', () => {
        if (soundEnabled) {
            ambientAudio.play().catch(e => console.log('Audio play prevented:', e));
        }
    }, { once: true });
    
    // Também tenta iniciar automaticamente
    ambientAudio.play().catch(e => console.log('Autoplay prevented, waiting for user interaction'));
    
    // Controles de Som
    const soundToggle = document.getElementById('sound-toggle');
    const soundOnIcon = soundToggle.querySelector('.sound-on');
    const soundOffIcon = soundToggle.querySelector('.sound-off');
    
    soundToggle.addEventListener('click', () => {
        soundEnabled = !soundEnabled;
        
        if (soundEnabled) {
            ambientAudio.play();
            soundOnIcon.style.display = 'block';
            soundOffIcon.style.display = 'none';
            soundToggle.classList.remove('active');
        } else {
            ambientAudio.pause();
            soundOnIcon.style.display = 'none';
            soundOffIcon.style.display = 'block';
            soundToggle.classList.add('active');
        }
    });
    
    // Controles de Aparições
    const apparitionsToggle = document.getElementById('apparitions-toggle');
    const apparitionsOnIcon = apparitionsToggle.querySelector('.apparitions-on');
    const apparitionsOffIcon = apparitionsToggle.querySelector('.apparitions-off');
    
    apparitionsToggle.addEventListener('click', () => {
        apparitionsEnabled = !apparitionsEnabled;
        
        if (apparitionsEnabled) {
            apparitionsOnIcon.style.display = 'block';
            apparitionsOffIcon.style.display = 'none';
            apparitionsToggle.classList.remove('active');
        } else {
            // Remove todas as aparições existentes
            const apparitions = container.querySelectorAll('.apparition');
            apparitions.forEach(app => {
                if (app.dataset.zone) {
                    occupiedZones.delete(app.dataset.zone);
                }
                app.remove();
            });
            apparitionsOnIcon.style.display = 'none';
            apparitionsOffIcon.style.display = 'block';
            apparitionsToggle.classList.add('active');
        }
    });
    
    // Inicia o ciclo de horror
    horrorLoop();
});
