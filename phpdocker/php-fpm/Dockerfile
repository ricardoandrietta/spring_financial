FROM phpdockerio/php:8.3-fpm
WORKDIR "/application"

RUN apt-get update \
    && apt-get -y --no-install-recommends install \
        vim \
        curl \
        wget \
        git \
        php8.3-mysql \
        php8.3-redis \
        php8.3-sqlite3 \
        php8.3-xdebug \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/* /usr/share/doc/*

# Install NVM
ENV NVM_DIR /usr/local/nvm
RUN mkdir -p $NVM_DIR
# Replace with the NVM version you want to use
ENV NVM_VERSION v0.40.1

# Install NVM
RUN curl -o- https://raw.githubusercontent.com/nvm-sh/nvm/$NVM_VERSION/install.sh | bash

# Add NVM to path
ENV NODE_VERSION 22
ENV PATH $NVM_DIR/versions/node/v$NODE_VERSION/bin:$PATH

# Set up shell to source NVM
RUN echo "source $NVM_DIR/nvm.sh && \
    nvm install $NODE_VERSION && \
    nvm alias default $NODE_VERSION && \
    nvm use default" | bash

# Updating NPM
RUN npm install npm@latest -g