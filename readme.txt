wp-admin-posts-extended/
│
├── admin/
│   ├── AdminFiltersController.php
│   ├── AdminExportController.php
│   ├── AdminAssets.php (opcional)
│   │
│   └── views/
│       ├── tag-filter.php
│       └── export-button.php
│
├── domain/
│   ├── PostFilter.php
│   ├── PostCriteria.php
│   └── PostRepositoryInterface.php
│
├── infrastructure/
│   └── wordpress/
│       ├── WpPostRepository.php
│       ├── AdminQueryModifier.php
│       └── Request.php
│
├── bootstrap/
│   └── admin.php
│
└── wp-admin-posts-extended.php