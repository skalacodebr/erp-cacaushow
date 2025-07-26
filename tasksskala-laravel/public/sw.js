const CACHE_NAME = 'skala-cliente-v1';
const urlsToCache = [
  '/cliente/login',
  '/cliente/dashboard',
  '/css/app.css',
  '/js/app.js',
  '/favicon.ico'
];

// Instalação do Service Worker
self.addEventListener('install', event => {
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then(cache => {
        console.log('Cache aberto');
        return cache.addAll(urlsToCache);
      })
      .catch(error => {
        console.error('Erro ao adicionar URLs ao cache:', error);
      })
  );
});

// Ativação do Service Worker
self.addEventListener('activate', event => {
  event.waitUntil(
    caches.keys().then(cacheNames => {
      return Promise.all(
        cacheNames.map(cacheName => {
          if (cacheName !== CACHE_NAME) {
            console.log('Removendo cache antigo:', cacheName);
            return caches.delete(cacheName);
          }
        })
      );
    })
  );
});

// Interceptação de requisições
self.addEventListener('fetch', event => {
  // Ignorar requisições não-GET
  if (event.request.method !== 'GET') {
    return;
  }

  // Estratégia: Network First, fallback para cache
  event.respondWith(
    fetch(event.request)
      .then(response => {
        // Verifica se a resposta é válida
        if (!response || response.status !== 200 || response.type !== 'basic') {
          return response;
        }

        // Clone a resposta
        const responseToCache = response.clone();

        // Adiciona ao cache
        caches.open(CACHE_NAME)
          .then(cache => {
            // Não cacheia URLs com query strings de autenticação
            if (!event.request.url.includes('?') && !event.request.url.includes('/api/')) {
              cache.put(event.request, responseToCache);
            }
          });

        return response;
      })
      .catch(() => {
        // Se falhar, tenta buscar do cache
        return caches.match(event.request)
          .then(response => {
            if (response) {
              return response;
            }
            
            // Se for uma página HTML, retorna a página offline ou login
            if (event.request.headers.get('accept').includes('text/html')) {
              return caches.match('/cliente/login');
            }
          });
      })
  );
});

// Mensagens do app
self.addEventListener('message', event => {
  if (event.data && event.data.type === 'SKIP_WAITING') {
    self.skipWaiting();
  }
});