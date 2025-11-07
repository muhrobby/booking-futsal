# Project File Organization Plan

## Struktur yang Akan Dibuat

```
booking-futsal/
├── 📋 DOCUMENTATION (di root - main guides)
│   ├── README.md                          ← Main project README
│   ├── START_HERE.md                      ← Entry point
│   ├── QUICKSTART.md                      ← Quick start guide
│   └── INDEX.md                           ← Navigation guide
│
├── 📁 .organization/  (New - Organized docs)
│   ├── 📂 documentation/
│   │   ├── PROJECT_STRUCTURE.md           ← Project overview
│   │   ├── ARCHITECTURE.md                ← System architecture
│   │   ├── DATABASE_SCHEMA.md             ← Database docs
│   │   └── API_REFERENCE.md               ← API docs (if any)
│   │
│   ├── 📂 deployment/
│   │   ├── DEPLOYMENT_GUIDE.md            ← Main deployment guide
│   │   ├── DOCKER_SETUP.md                ← Docker instructions
│   │   ├── GITHUB_ACTIONS.md              ← CI/CD setup
│   │   ├── ENVIRONMENT_SETUP.md           ← .env configuration
│   │   └── PRODUCTION_CHECKLIST.md        ← Pre-deployment
│   │
│   ├── 📂 guides/
│   │   ├── SECURITY_GUIDE.md              ← Security testing
│   │   ├── LOAD_TESTING_GUIDE.md          ← Performance testing
│   │   ├── DEVELOPMENT_SETUP.md           ← Dev environment
│   │   ├── TESTING_GUIDE.md               ← Unit/Feature tests
│   │   └── TROUBLESHOOTING.md             ← Common issues
│   │
│   ├── 📂 tools/
│   │   ├── LOAD_TEST_CONFIG.md            ← Artillery config guide
│   │   ├── SCRIPTS.md                     ← Available scripts
│   │   └── COMMANDS_REFERENCE.md          ← Common commands
│   │
│   └── 📄 INDEX.md                        ← Organization index
│
├── 📁 .azure/ (Azure & Reports)
│   ├── FINAL-REPORT.md                    ← Final project report
│   ├── LOAD-TESTING-GUIDE.md              ← Load testing
│   ├── security-and-load-testing.md       ← Security audit
│   ├── postgresql-migration-complete.md   ← Migration report
│   └── [other azure reports]
│
├── 📁 docs/ (Keep existing - Reference)
│   └── [Existing documentation files]
│
├── 📁 tools/
│   └── [Scripts and tools]
│
├── 🔧 Configuration Files (root)
│   ├── load-test.yml
│   ├── load-test-processor.js
│   ├── run-load-tests.sh
│   ├── docker-compose.yml
│   ├── Dockerfile
│   ├── vite.config.js
│   ├── tailwind.config.js
│   └── postcss.config.js
│
└── 📂 Standard Laravel Folders
    ├── app/
    ├── bootstrap/
    ├── config/
    ├── database/
    ├── public/
    ├── resources/
    ├── routes/
    ├── storage/
    ├── tests/
    └── vendor/
```

## Files to Move

### To .organization/documentation/
- AUTO_UPDATE_SETUP.md → setup/AUTO_UPDATE.md
- docs/PROJECT-STRUCTURE.md → PROJECT_STRUCTURE.md
- docs/IMPLEMENTATION-GUIDE.md → IMPLEMENTATION.md

### To .organization/deployment/
- DEPLOYMENT_GUIDE.md
- DEPLOYMENT_QUICK_START.md
- DEPLOY_AS_ROBBY.md
- DEPLOY_SIMPLIFIED.md
- CICD_DEPLOYMENT.md
- CI_CD_COMPLETE_GUIDE.md
- GITHUB_ACTIONS_SETUP.md
- GITHUB_ACTIONS_READY.md
- GITHUB_SECRETS_SETUP.md
- DOCKER_VERIFICATION.md

### To .organization/guides/
- docs/QUICK_TEST_GUIDE.md
- docs/TEST_CASES.md
- docs/DESIGN-SYSTEM.md

### Keep in Root (Main Entry Points)
- README.md
- START_HERE.md
- QUICKSTART.md
- INDEX.md

### Keep in Folders
- .azure/ (Azure-specific reports)
- docs/ (Additional reference)
- docker/ (Docker files)
