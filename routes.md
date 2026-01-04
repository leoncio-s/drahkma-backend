## WEB
- [GET] /frontend

## API
### Auth
- [POST]         /api/v1/auth/login
- [POST]         /api/v1/auth/forget-password
- [POST]         /forget-password/{email}

### User
- [GET|POST|PUT] /api/v1/user
- [GET]          /api/v1/user/email/verify/{$token}
- [PUT]          /api/v1/user/password

### Item
- [GET|POST|PUT] /api/v1/item
- [DELETE]       /api/v1/item/{id}
- [GET]          /api/v1/item/inflow
- [GET]          /api/v1/item/outflow
- [GET]          /api/v1/item/amounts

### Categories
- [GET|POST|PUT] /api/v1/categories
- [DELETE]       /api/v1/categories/{id}

### Cards
- [GET|POST|PUT] /api/v1/cards
- [DELETE]       /api/v1/cards/{id}

### Bank Accounts
- [GET|POST|PUT] /api/v1/banks
- [DELETE]       /api/v1/banks/{id}