# Projeto Ecommerce Magento, instalação simples

## Requisitos
- Apache2
- PHP 8.3
- MySql e OpenSearch (docker-compose)

### Libs necessarias para o PHP

```shell
bash php/libs.sh
```

### Instalação do Magento

- Apache

https://www.digitalocean.com/community/tutorials/how-to-install-linux-apache-mysql-php-lamp-stack-on-ubuntu-20-04-pt

| Seguir tutorial e instalar apenas o Apache.

- Services necessarios

```shell
docker compose up -d --build
```

- Magento
```php
php bin/magento setup:install --base-url=http://127.0.0.1:8082 \
--db-host=127.0.0.1:3306 --db-name=magento --db-user=root --db-password=admin123 \
--admin-firstname=Magento --admin-lastname=User --admin-email=user@example.com \
--admin-user=admin --admin-password=admin123 --language=en_US \
--currency=USD --timezone=America/Chicago --use-rewrites=1 \
--search-engine=opensearch \
--opensearch-host=127.0.0.1 \
--opensearch-port=9200 \
--opensearch-index-prefix=magento2 \
--opensearch-timeout=15
```
| Anote a url do painel administrativo que sera apresentada no final do log. Ex: [SUCCESS]: Magento Admin URI: /admin_06x66s1

| Erro web-site remover app/etc/env.php, o magento cria ele novamente.

### Acessando o Magento

```php
php -S 127.0.0.1:8082 -t ./pub/ ./phpserver/router.php
```
### Comandos uteis

- Aumentar memoria do vm/docker
```shell
sudo sysctl -w vm.max_map_count=262144 
```

- Limpar cache e atualizar indices
```
bin/magento indexer:reindex && bin/magento cache:clean
```