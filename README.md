# Stripe Integration Project / Projekt Integracji Stripe

A Symfony-based application for managing clients, orders, and processing Stripe payments with webhook support.

Aplikacja oparta na Symfony do zarządzania klientami, zamówieniami i procesowania płatności Stripe wraz z obsługą webhooków.

---

## English Version

### Tech Stack
- **PHP:** 8.4+
- **Framework:** Symfony 8.0
- **Database:** MySQL/MariaDB (via Doctrine ORM)
- **Payment Provider:** Stripe (via `stripe/stripe-php` v20.1)

### Features
- **Client Management:** Storing email and Stripe Customer ID.
- **Order Tracking:** Managing order amounts, statuses, and Stripe Session IDs.
- **Webhook Processor:** An extensible system using a tagged iterator for handling different Stripe event types.
- **Data Fixtures:** Pre-configured client data for development (using Faker).

### Environment Variables
Configure the following keys in your `.env.local` file:
```env
DATABASE_URL="mysql://user:password@127.0.0.1:3306/db_name?serverVersion=8.0"
STRIPE_SECRET_KEY="sk_test_..."
STRIPE_WEBHOOK_SECRET="whsec_..."
DEFAULT_URI="https://your-domain.com"
```

### Installation
1. **Install dependencies:**
   ```bash
   composer install
   ```
2. **Run migrations:**
   ```bash
   bin/console doctrine:migrations:migrate
   ```
3. **Load fixtures (optional):**
   ```bash
   bin/console doctrine:fixtures:load
   ```

### Webhooks
To add a new Stripe event handler, create a class implementing `App\Stripe\Event\StripeWebhookInterface`. Thanks to Symfony's autoconfiguration, it will be automatically injected into the `StripeWebhookProcessor`.

---

## Polska Wersja

### Stos Technologiczny
- **PHP:** 8.4+
- **Framework:** Symfony 8.0
- **Baza Danych:** MySQL/MariaDB (przez Doctrine ORM)
- **Dostawca Płatności:** Stripe (przez `stripe/stripe-php` v20.1)

### Funkcjonalności
- **Zarządzanie Klientami:** Przechowywanie adresów e-mail oraz identyfikatorów Stripe Customer ID.
- **Obsługa Zamówień:** Zarządzanie kwotami, statusami oraz identyfikatorami sesji Stripe.
- **Procesor Webhooków:** Rozszerzalny system wykorzystujący `tagged_iterator` do obsługi różnych typów zdarzeń Stripe.
- **Dane Testowe (Fixtures):** Skonfigurowane dane klientów do celów deweloperskich (przy użyciu biblioteki Faker).

### Zmienne Środowiskowe
Skonfiguruj poniższe klucze w pliku `.env.local`:
```env
DATABASE_URL="mysql://użytkownik:hasło@127.0.0.1:3306/nazwa_bazy?serverVersion=8.0"
STRIPE_SECRET_KEY="sk_test_..."
STRIPE_WEBHOOK_SECRET="whsec_..."
DEFAULT_URI="https://twoja-domena.pl"
```

### Instalacja
1. **Instalacja zależności:**
   ```bash
   composer install
   ```
2. **Uruchomienie migracji:**
   ```bash
   bin/console doctrine:migrations:migrate
   ```
3. **Załadowanie danych testowych (opcjonalnie):**
   ```bash
   bin/console doctrine:fixtures:load
   ```

### Webhooki
Aby dodać nową obsługę zdarzenia Stripe, utwórz klasę implementującą interfejs `App\Stripe\Event\StripeWebhookInterface`. Dzięki autokonfiguracji Symfony, zostanie ona automatycznie wstrzyknięta do usługi `StripeWebhookProcessor`.

---

## License / Licencja
MIT
