# S.I.K.A.P. Hub V2 - Technical Debt & Future Patches

## Phase 2 / Phase 4: Registration & Onboarding Gap
* **Issue:** Currently, registration only populates the `users` table. `job_seekers` and `employers` tables remain empty, causing Foreign Key constraint failures during profile updates.
* **Solution Needed:** Implement a dedicated Onboarding UI flow after login, OR implement MySQL Database Triggers to auto-create blank profile rows upon registration.

## Phase 5: Admin Authorization Flow
* **Issue:** Admin role is currently manually assigned in the database for testing.
* **Solution Needed:** Build a secure SuperAdmin dashboard to elevate user roles, or create a hidden, heavily secured registration endpoint strictly for PESO staff.