



# LG Electronics – Dashboard de Produção (desafio técnico)

> **Nota ao recrutador:**
> Este projeto foi desenvolvido exclusivamente para avaliação no processo seletivo da LG Electronics. O objetivo foi demonstrar domínio de arquitetura, testes e boas práticas em Laravel, indo além do necessário para um CRUD simples. Caso fosse um projeto real, pontual e de escopo restrito, a abordagem seria mais pragmática e focada em velocidade de entrega. Aqui, a intenção foi mostrar como estruturaria um projeto escalável, testável e pronto para crescer, caso fosse necessário atender demandas futuras ou equipes maiores.



## Motivação da feature de IA

Durante a leitura da descrição da vaga e na entrevista, ficou claro que a empresa valoriza soluções com inteligência artificial e automação de análises. Por isso, incluí uma feature de IA: o serviço `GeminiService`, capaz de gerar observações automáticas sobre os dados de produção. Optei por integrar o **Gemini** (Google) ao invés da OpenAI, pois o Gemini oferece um modelo gratuito, facilitando a demonstração sem custos para o avaliador e sem necessidade de chave paga.



Dashboard de eficiência de produção da **Planta A** (dados de exemplo: janeiro de 2026). O projeto foi desenhado para servir como prova de conceito e vitrine de boas práticas — a arquitetura robusta é intencional para demonstrar padrões como Service Layer, Repositories, Interfaces, injeção de dependência e testes unitários/feature.

## Stack principal
- Backend: PHP 7.4 / Laravel 7
- Banco: MySQL 8
- Frontend: Blade + Tailwind CSS (CDN) + Chart.js
- Testes: PHPUnit


## Por que uma arquitetura robusta?
- O objetivo foi mostrar domínio de padrões e práticas que facilitam manutenção, testes e evolução do sistema.
- Em projetos reais, separar responsabilidades (service, repository, interface) é fundamental para escalar e trabalhar em equipe.
- Aqui você encontrará exemplos práticos de:
  - Service Layer (`app/Services/*`) para regras de negócio e queries testáveis
  - Repositories e interfaces para desacoplar persistência
  - Abstração de integrações externas via interfaces (ex.: `GeminiServiceInterface`)
  - Testes unitários e de integração (feature tests)

Arquitetura (resumo)

app/
├── Productivity.php                    # Model – constantes de linhas, scopes e accessors
├── Services/
│   ├── ProductivityService.php         # Regras de negócio / agregações
│   └── GeminiService.php               # Integração com provedor de IA (implementação)
├── Repositories/                       # Camada de persistência (interface + impl.)
└── Http/Controllers/
    └── DashboardController.php         # Recebe request → Service → View

As linhas de produto são definidas como constantes em `Productivity` (`LINE_*` e `PRODUCT_LINES`) para garantir consistência entre seeders, validações e views.

Feature de IA
- Existe um serviço chamado `GeminiService` (e sua interface `GeminiServiceInterface`) que ilustra como integrar um provedor de modelos de linguagem para gerar análises ou observações automáticas sobre os dados de produção. Principais características:
  - Abstração via interface: você pode trocar o provedor (ou mocká-lo) sem alterar a lógica da aplicação.
  - Implementação pensada para ser facilmente testável (os testes unitários mockam a integração).
  - Uso típico: gerar um resumo textual ou insights (ex.: "A linha X teve queda de eficiência nos últimos 3 dias") com base nas métricas agregadas pela `ProductivityService`.

Importante: nesta demo a integração com IA é demonstrativa — não há credenciais em repositório. Em produção, configure a chave/endpoint via `.env` e implemente um adaptador seguro.

Pré-requisitos
- Docker & Docker Compose (recomendado para manter parity com ambiente de desenvolvimento)
- Ou: PHP 7.4+, Composer e MySQL 8 para rodar localmente

Rodando com Docker Compose (recomendado)

1) Clone o repo e entre na pasta:

```bash
git clone <url-do-repositorio>
cd lg-dashboard-test
```

2) Copie o arquivo de ambiente (já preparado para Docker) e suba os containers:

```bash
cp .env.example .env
docker compose up -d --build
```

3) Execute comandos dentro do container `app` para instalar dependências, gerar a chave da aplicação, rodar migrations e popular o banco:

```bash
docker compose exec app composer install --no-interaction --prefer-dist
docker compose exec app php artisan key:generate --force
docker compose exec app php artisan migrate --force
docker compose exec app php artisan db:seed --force
```

4) Abra no navegador: http://localhost:8000

Para parar os containers:

```bash
docker compose down            # mantém os volumes
docker compose down -v         # limpa volumes (inclui dados do MySQL)
```

Instalação local (sem Docker)

```bash
git clone <url-do-repositorio>
cd lg-dashboard-test
composer install
cp .env.example .env
php artisan key:generate
# ajuste as variáveis de DB no .env para apontar ao seu MySQL local
php artisan migrate
php artisan db:seed
php artisan serve
```

Banco de dados e seed
- A migration `create_productivities_table` cria a tabela `productivities` com índices para plant/product_line e data.
- O seeder popula registros para todos os dias úteis de janeiro de 2026 — ideal para testar gráficos e filtros.

Fórmula de eficiência

Eficiência (%) = ((Produzido − Defeitos) / Produzido) × 100

Intervalos de observação
- ≥ 95%: Ótimo
- 85% – 94,9%: Regular
- < 85%: Crítico

Testes

Execute a suíte de testes com:

```bash
docker compose exec app vendor/bin/phpunit
# ou, localmente:
vendor/bin/phpunit
```

A suíte cobre:
- Cálculo de eficiência (casos borda: zero, 100%, arredondamento, alto volume)
- Constantes do model (`PRODUCT_LINES`, `LINE_*`)
- Validações e accessors
- Feature tests da rota principal (`/`)


## Notas finais e boas práticas demonstradas
- Código organizado para ser fácil de testar e estender.
- Integrações (como a de IA) são desacopladas por interfaces para permitir substituição e mock nos testes.
- Uso de Seeders e Factories para dados previsíveis em testes e demo.
- Arquitetura pensada para um time: separar controllers (HTTP), services (negócio) e repositórios (persistência).

---

Fique à vontade para perguntar qualquer detalhe técnico, decisão de arquitetura ou pedir exemplos de código/testes específicos.


