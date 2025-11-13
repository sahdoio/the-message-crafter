FROM node:18

WORKDIR /var/www

ADD . /var/www

# RUN npm install

CMD tail -f /dev/null