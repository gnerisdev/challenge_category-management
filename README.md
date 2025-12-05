# Resumo
Gerenciador de Categorias é uma aplicação que tem o objetivo de criar e gerenciar categorias de produto de uma maneira fácil e interativa.

# Funcionalidades
- Cadastro de categoria/subcategoria;
- Edição de categoria/subcategoria;
- Exclusão de categoria/subcategoria;
- Restauração de categoria/subcategoria;
- Exclusão permanente de categoria/subcategoria;
- Reordenação de categoria/subcategoria;
- Visualização de categoria/subcategoria;
- Dashboard informativo.

# Tecnologias utilizadas
- Laravel 11
- Vue 3
- Tailwind CSS
- MySQL
- Docker
- Docker Compose
- Git
- GitHub

# Como executar a aplicação
*Requisito: ter o Docker instalado na máquina*

1- Clonar o repositório  
    ```bash
    git clone https://github.com/gnerisdev/challenge_category-management/
    cd challenge_category-management
    ```

2- Buildar os containers Docker  
    ```bash
    docker-compose build
    ```

3- Iniciar os containers Docker  
    ```bash
    docker-compose up -d
    ```

4- Instalar as dependências do Laravel/PHP  
    ```bash
    docker exec -it app_laravel bash
    composer install
    ```

5- Gerar a chave do Laravel  
    ```bash
    docker exec -it app_laravel bash
    php artisan key:generate
    ```

6- Executar as migrations  
    ```bash
    docker exec -it app_laravel bash
    php artisan migrate
    ```

7- Reniciar os containers
    docker-compose down
    docker-compose up -d