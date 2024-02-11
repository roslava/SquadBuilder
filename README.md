# API Documentation

## Introduction
This API allows users to manage players, their positions, and skills within a sports team management system. It provides functionality to add, update, list, and delete players, as well as select the best team based on specific criteria.

## Models

### Player
- **Attributes**: `id`, `name`, `position`
- **Relationships**: `skills` (many-to-many relationship through `player_skill` pivot table)

### Skill
- **Attributes**: `id`, `skill`, `value`
- **Relationships**: `players` (many-to-many relationship through `player_skill` pivot table)

## Routes

### Player Routes
- `POST /api/player`: Create a new player with skills.
- `PUT /api/player/{playerId}`: Update an existing player and their skills.
- `DELETE /api/player/{playerId}`: Delete a player. Requires Bearer token authorization.
- `GET /api/player`: List all players with their skills.

### Team Routes
- `POST /api/team/process`: Select the best team based on provided requirements.

## Services

### TeamSelectionService
- `selectBestTeamFromRequirements(array $requirements)`: Selects the best team based on the provided requirements. Returns a collection of players.

## Controllers

### PlayerController
- Handles requests related to players, such as creating, updating, deleting, and listing players.

### SkillController
- Handles requests related to skills, such as creating and attaching skills to players.

## Middleware

### check-static-bearer-token
- Ensures that the DELETE request for a player includes the correct Bearer token in the Authorization header.

## Factories

- `PlayerFactory`: Generates fake data for players.
- `SkillFactory`: Generates fake data for skills.
- `UserFactory`: Generates fake data for users.

## Seeders

- `SkillsTableSeeder`: Seeds the skills table with predefined skill names.
- `PlayersTableSeeder`: Seeds the players table with fake players and their skills.

## Tests

- `ApiEndpointsTest`: Tests the API endpoints for creating, updating, deleting, and listing players.
- `SkillControllerTest`: Tests the skill creation and association with players.
- `TeamSelectionTest`: Tests the team selection logic based on skill requirements.