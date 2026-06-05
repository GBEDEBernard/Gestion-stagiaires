# Phase 3: Real-Time Broadcasting with Laravel Echo

## Overview

Phase 3 implements real-time message broadcasting for task discussions using Laravel Echo and WebSocket support. The system gracefully falls back to polling when WebSocket is unavailable (e.g., behind ngrok tunnels).

## Architecture

### Broadcast Events
- **TaskMessageCreated**: New message in a task thread
- **TaskMessageReactionAdded**: Emoji reaction on a message
- **TaskMessageRead**: Read receipt/cursor update
- **UserIsTyping**: Typing indicator (optional UI)

All events broadcast to private channel `task.{id}` visible only to authorized users (owner, supervisors, admins).

### Channel Authorization
File: `routes/channels.php`

Access control:
- Task owner: always authorized
- Supervisors/Admins: authorized for their scoped tasks
- Others: denied

### Frontend Integration
- **Echo Client**: `resources/js/echo.js` initializes WebSocket connection via Pusher
- **Fallback**: Polling continues running (4s interval) if WebSocket unavailable
- **Alpine Integration**: `taskChat()` component listens to Echo events in `setupEcho()` method

## Configuration

### Environment Variables
```bash
# Broadcast driver
BROADCAST_CONNECTION=log              # Set to 'pusher' in production
BROADCAST_DRIVER=log

# Pusher/WebSocket config (local development)
PUSHER_APP_ID=gestion-stagiaires
PUSHER_APP_KEY=gestion-stagiaires-key
PUSHER_APP_SECRET=gestion-stagiaires-secret
PUSHER_HOST=localhost
PUSHER_PORT=6001
PUSHER_SCHEME=http

# Reverb config (if using Laravel Reverb instead of Pusher)
REVERB_APP_ID=gestion-stagiaires
REVERB_APP_KEY=gestion-stagiaires-key
REVERB_APP_SECRET=gestion-stagiaires-secret
REVERB_HOST=localhost
REVERB_PORT=8080
REVERB_SCHEME=http

# Vite config (frontend)
VITE_PUSHER_APP_KEY=gestion-stagiaires-key
VITE_PUSHER_APP_CLUSTER=mt
VITE_PUSHER_HOST=localhost
VITE_PUSHER_PORT=6001
VITE_PUSHER_SCHEME=http
VITE_BROADCAST_DRIVER=pusher
```

### Broadcasting Config
File: `config/broadcasting.php`

Defines connections for:
- Pusher (development/local with BeyondCode Pusher Fake Server)
- Reverb (production WebSocket server)
- Redis (alternative for clustering)
- Log (fallback/testing)

## Development Setup

### Option 1: Pusher Fake Server (Recommended for Local)

```bash
# Install beyond-code pusher fake server
npm install -g pusher-fake-server

# Run it (port 6001)
pusher-fake-server

# Update .env:
BROADCAST_CONNECTION=pusher
BROADCAST_DRIVER=pusher
PUSHER_HOST=localhost
PUSHER_PORT=6001
PUSHER_SCHEME=http
```

### Option 2: Laravel Reverb (Production-Like)

```bash
# Install Reverb (requires composer)
composer require laravel/reverb

# Publish config
php artisan vendor:publish --provider="Laravel\Reverb\ReverBServiceProvider"

# Start server (port 8080)
php artisan reverb:start

# Update .env to use Reverb config
```

### Option 3: Polling Only (No WebSocket)

Keep `BROADCAST_CONNECTION=log` - frontend will use polling fallback. Useful for ngrok environments where WebSocket ports don't pass through.

## Testing

Echo gracefully handles connection failures:
- If WebSocket unavailable: console warning, polling continues
- If WebSocket available: events subscribed, polling still active
- Either way: real-time updates work

```javascript
// In browser console:
console.log(window.Echo);  // null if failed, Echo instance if succeeded
```

## Files

### New Files
- `app/Events/TaskMessageCreated.php` - Message creation event
- `app/Events/TaskMessageReactionAdded.php` - Reaction event
- `app/Events/TaskMessageRead.php` - Read receipt event
- `app/Events/UserIsTyping.php` - Typing indicator event
- `config/broadcasting.php` - Broadcasting configuration
- `routes/channels.php` - Channel authorization
- `resources/js/echo.js` - Echo client initialization
- `app/Providers/BroadcastServiceProvider.php` - Broadcast service provider

### Modified Files
- `app/Http/Controllers/TaskMessageController.php` - Added event broadcasting
- `app/Providers/AppServiceProvider.php` - Load broadcast channels
- `resources/views/tasks/partials/detail.blade.php` - Alpine Echo integration
- `resources/js/app.js` - Import echo.js
- `.env` - Broadcasting configuration

## Security Notes

1. **Private Channels**: All task channels are private (`task.{id}`)
2. **Authorization**: Validated in `routes/channels.php` before subscription
3. **Scalability**: Use Redis driver with clustering for multi-server deployments
4. **ngrok Compatibility**: WebSocket may not work through ngrok tunnels (port 8080 blocked)

## Phase 3 Completion Status

✅ Event classes created and integrated
✅ Channel authorization implemented
✅ Echo client-side setup
✅ Alpine component Echo listener integration
✅ Fallback polling maintained
✅ Environment configuration complete
✅ Tests passing with 'log' driver

## Next Steps (Phase 3 Completion)

- [ ] Install Pusher Fake Server or Reverb for local development
- [ ] Test WebSocket connections with browser DevTools
- [ ] Monitor for race conditions between polling and Echo events
- [ ] Implement typing indicators UI (optional)
- [ ] Production deployment with Redis/Reverb

## Notes

- Polling (4s) continues to run even with Echo active
- This is intentional: polling is a reliable fallback for disconnects/ngrok
- In high-traffic scenarios, consider replacing polling with purely Echo-based updates
- Message deduplication by ID prevents duplicate processing
