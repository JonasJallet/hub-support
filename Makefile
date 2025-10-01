# ─────────────────────────────
# 📁 Paths
# ─────────────────────────────
BACK_DIR  := api
FRONT_DIR := web-app
FRONT_COMPOSE := docker compose -f $(FRONT_DIR)/compose.yaml

# ─────────────────────────────
# 📌 Api Commands
# ─────────────────────────────

api-start:
	@$(MAKE) -C $(BACK_DIR) start

api-up:
	@$(MAKE) -C $(BACK_DIR) up

api-down:
	@$(MAKE) -C $(BACK_DIR) down

api-build:
	@$(MAKE) -C $(BACK_DIR) build

api-logs:
	@$(MAKE) -C $(BACK_DIR) logs

api-sh:
	@$(MAKE) -C $(BACK_DIR) sh

# ─────────────────────────────
# 🌐 Web-app Commands
# ─────────────────────────────

web-app-up:
	@echo "Starting web-app..."
	@$(FRONT_COMPOSE) up --build -d

web-app-down:
	@echo "Stopping web-app..."
	@$(FRONT_COMPOSE) down

web-app-logs:
	@$(FRONT_COMPOSE) logs -f

web-app-restart: web-app-down web-app-up

# ─────────────────────────────
# 🌍 Combined Commands
# ─────────────────────────────

start: api-start web-app-up
	@echo "Api and Web-app have started."

up: api-up web-app-up
	@echo "Api and Web-app are up."

down: api-down web-app-down
	@echo "Api and Web-app are down."

logs:
	@echo "Tailing logs..."
	@$(MAKE) -C $(BACK_DIR) logs & $(FRONT_COMPOSE) logs -f

restart: down start

# ─────────────────────────────
# 📚 Help
# ─────────────────────────────

help:
	@echo "Available commands:"
	@echo "  make api-start     → Build and start backend"
	@echo "  make api-up        → Start backend containers only"
	@echo "  make web-app-up    → Start frontend containers"
	@echo "  make start         → Start api (with build) && web-app"
	@echo "  make down          → Stop everything"
	@echo "  make logs          → Show logs for both"
	@echo "  make restart       → Restart both"
