# 📬 Message Crafter

**Message Crafter** is a PHP 8.4-based application that integrates with the Meta WhatsApp API using the **Strategy Pattern** to manage and send messages. Built with PostgreSQL, Node.js 22, Inertia.js, and Vue 3.

📺 **Watch the introduction video:**  
[![Watch on YouTube](https://img.youtube.com/vi/d31CAhquhXw/hqdefault.jpg)](https://youtu.be/d31CAhquhXw)

---

## 🧱 Stack

- **PHP 8.4**
- **PostgreSQL**
- **Node.js 22**
- **Laravel (with Inertia.js + Vue 3)**
- **Docker + Make**

---

## 🚀 First-Time Setup

If you're running the project for the first time, follow these steps:

```bash
# 1. Build, install dependencies, migrate DB, and start Vite
make go

# 2. Press CTRL+C to stop Vite (after confirming it built successfully)

# 3. Seed the database
make db-seed

# 4. Restart Vite
make vite
```

---

## 🛠️ Available Commands

| Command               | Description                                  |
|----------------------|----------------------------------------------|
| `make go`            | Full setup (build, install, migrate, vite)   |
| `make go-test`       | Setup test environment from scratch          |
| `make up`            | Start the containers                         |
| `make down`          | Stop all containers                          |
| `make setup`         | Install PHP and Node.js dependencies         |
| `make sh`            | Open a shell in the PHP container            |
| `make node-sh`       | Open a shell in the Node.js container        |
| `make test`          | Run tests with coverage                      |
| `make paratest`      | Run tests in parallel (10 processes)         |
| `make test-coverage` | Generate HTML coverage report                |
| `make db-migrate`    | Run database migrations                      |
| `make db-seed`       | Seed the database                            |
| `make db-rollback`   | Rollback last migration                      |
| `make db-reset`      | Rollback + migrate + seed                    |
| `make logs`          | Show recent container logs                   |
| `make log`           | Tail Laravel logs                            |
| `make horizon`       | Start Laravel Horizon                        |
| `make clear`         | Clear all Laravel caches                     |
| `make vite`          | Start the Vite dev server                    |

---

## 📦 Meta Integration

Message sending is powered by **Strategy Pattern** to allow easy extension and switching of behavior depending on message type, channel, or context.

---

## 📂 Project Structure

- `msg-crafter`: Laravel app (PHP + Inertia + Vue)
- `msg-crafter-nodejs`: Node.js service (optional support tooling)
- `.env`: Base environment config
- `.env.testing`: Testing environment config (under `/src`)

---

## 📄 License

MIT
