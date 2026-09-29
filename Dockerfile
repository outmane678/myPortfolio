# Utilise l'image Nginx officielle
FROM nginx:alpine

# Supprime la configuration par défaut de Nginx
RUN rm -rf /usr/share/nginx/html/*

# Copie ton site dans le dossier Nginx
COPY . /usr/share/nginx/html

# Expose le port 80
EXPOSE 80

# Lance Nginx
CMD ["nginx", "-g", "daemon off;"]
