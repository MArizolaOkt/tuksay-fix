# TUKSAY ERP — Agent Execution Specification

> **Agent Instructions:** Execute each task sequentially by ID. Validate each acceptance criteria before proceeding to the next task. All tasks are grouped by phase and layer (Database → Models → Controllers → UI).

---

## PROJECT METADATA

```yaml
project_name: TUKSAY - Fresh Produce Supplier Management System
stack:
  backend: Laravel 12
  database: MySQL 8+
  frontend: Blade + Tailwind CSS
  charts: Chart.js
  auth: Laravel Breeze / Laravel Sanctum
  reporting: Laravel Excel + DomPDF
  deployment: Linux VPS
phases:
  - PHASE_1: Core Transaction System
  - PHASE_2: Analytics & Financial Intelligence
```

---

## BUSINESS CONTEXT

```yaml
characteristics:
  - Purchase prices change daily
  - Selling prices are relatively stable
  - Orders arrive one day before delivery
  - Market purchasing occurs early morning
  - Delivery happens same day as market purchase
  - Invoices billed weekly or monthly
  - Multiple customer orders consolidated into one purchasing list
  - Deliveries split per outlet
```

---

## PHASE 1 — CORE TRANSACTION SYSTEM

---

### DB-001 — Create `customers` Table

```yaml
task_id: DB-001
type: migration
table: customers
```

**Fields:**

| Column | Type | Notes |
|---|---|---|
| id | bigIncrements | Primary key |
| nama | string | Customer name |
| nama_perusahaan | string | Company name |
| alamat | text | Address |
| payment_method | enum | CASH, TOP7, TOP14, TOP30 |
| created_at | timestamp | |
| updated_at | timestamp | |

**Acceptance Criteria:**
- [ ] Migration runs without error
- [ ] `payment_method` enum enforced: `CASH`, `TOP7`, `TOP14`, `TOP30`
- [ ] Customer can have multiple outlets (via `customer_outlets.customer_id`)
- [ ] Customer can have multiple purchase orders (via `purchase_orders.customer_id`)

---

### DB-002 — Create `customer_outlets` Table

```yaml
task_id: DB-002
type: migration
table: customer_outlets
depends_on: DB-001
```

**Fields:**

| Column | Type | Notes |
|---|---|---|
| id | bigIncrements | Primary key |
| customer_id | foreignId | FK → customers.id |
| nama_outlet | string | Outlet name |
| created_at | timestamp | |
| updated_at | timestamp | |

**Acceptance Criteria:**
- [ ] `customer_id` foreign key set
- [ ] `onDelete('cascade')` enabled

---

### DB-003 — Create `barangs` Table

```yaml
task_id: DB-003
type: migration
table: barangs
```

**Fields:**

| Column | Type | Notes |
|---|---|---|
| id | bigIncrements | Primary key |
| nama | string | Product name |
| satuan | enum | kg, ikat, buah, pck |
| harga_jual | decimal(15,2) | Selling price |
| created_at | timestamp | |
| updated_at | timestamp | |

**Acceptance Criteria:**
- [ ] `satuan` enum enforced: `kg`, `ikat`, `buah`, `pck`
- [ ] Product can appear in many PO items
- [ ] Product can have many daily purchase prices

---

### DB-004 — Create `purchase_orders` Table

```yaml
task_id: DB-004
type: migration
table: purchase_orders
depends_on: [DB-001, DB-002]
```

**Fields:**

| Column | Type | Notes |
|---|---|---|
| id | bigIncrements | Primary key |
| no_po | string | Auto-generated, unique |
| no_ref | string | Customer reference number |
| customer_id | foreignId | FK → customers.id |
| customer_outlet_id | foreignId | FK → customer_outlets.id |
| tanggal | date | Order date |
| status | enum | baru, proses, selesai |
| created_at | timestamp | |
| updated_at | timestamp | |

**Auto Number Format:** `PO-000001` (zero-padded 6 digits)

**Acceptance Criteria:**
- [ ] `no_po` unique constraint enforced
- [ ] Auto-number generated on creation
- [ ] `status` enum: `baru`, `proses`, `selesai`

---

### DB-005 — Create `po_items` Table

```yaml
task_id: DB-005
type: migration
table: po_items
depends_on: [DB-003, DB-004]
```

**Fields:**

| Column | Type | Notes |
|---|---|---|
| id | bigIncrements | Primary key |
| purchase_order_id | foreignId | FK → purchase_orders.id |
| barang_id | foreignId | FK → barangs.id |
| qty | decimal(10,3) | Supports 0.5, 1.25, 2.75 |

**Acceptance Criteria:**
- [ ] `qty` is decimal, supports values like `0.5`, `1.25`, `2.75`
- [ ] `onDelete('cascade')` on `purchase_order_id`

---

### DB-006 — Create `harga_belis` Table

```yaml
task_id: DB-006
type: migration
table: harga_belis
depends_on: DB-003
```

**Fields:**

| Column | Type | Notes |
|---|---|---|
| id | bigIncrements | Primary key |
| barang_id | foreignId | FK → barangs.id |
| tanggal | date | Purchase date |
| harga_beli | decimal(15,2) | Purchase price |

**Constraints:**
```sql
UNIQUE (barang_id, tanggal)
```

**Acceptance Criteria:**
- [ ] Unique index on `(barang_id, tanggal)` — one price per product per day
- [ ] `updateOrCreate` used when inserting

---

### DB-007 — Create `surat_jalans` Table

```yaml
task_id: DB-007
type: migration
table: surat_jalans
depends_on: [DB-001, DB-002]
```

**Fields:**

| Column | Type | Notes |
|---|---|---|
| id | bigIncrements | Primary key |
| no_sj | string | Auto-generated, unique |
| customer_id | foreignId | FK → customers.id |
| customer_outlet_id | foreignId | FK → customer_outlets.id |
| tanggal | date | Delivery date |

**Auto Number Format:** `SJ-000001`

**Acceptance Criteria:**
- [ ] `no_sj` unique constraint enforced
- [ ] Auto-number generated on creation

---

### DB-008 — Create `invoices` Table

```yaml
task_id: DB-008
type: migration
table: invoices
depends_on: DB-001
```

**Fields:**

| Column | Type | Notes |
|---|---|---|
| id | bigIncrements | Primary key |
| no_invoice | string | Auto-generated, unique |
| customer_id | foreignId | FK → customers.id |
| tanggal | date | Invoice date |
| total_tagihan | decimal(15,2) | Total billed amount |
| status | enum | terbit, lunas |

**Auto Number Format:** `INV-000001`

**Acceptance Criteria:**
- [ ] `no_invoice` unique constraint enforced
- [ ] `status` enum: `terbit`, `lunas`

---

### DB-009 — Create `biaya_operasionals` Table

```yaml
task_id: DB-009
type: migration
table: biaya_operasionals
```

**Fields:**

| Column | Type | Notes |
|---|---|---|
| id | bigIncrements | Primary key |
| nama_biaya | string | Expense name |
| kategori | enum | Transport, Packaging, Komunikasi, Tak Terduga, Lain-lain |
| jumlah | decimal(15,2) | Amount |
| tanggal | date | Expense date |

**Acceptance Criteria:**
- [ ] `kategori` enum enforced

---

## ELOQUENT MODELS

---

### MODEL-001 — `Customer` Model

```yaml
task_id: MODEL-001
type: model
file: app/Models/Customer.php
depends_on: [DB-001, DB-002, DB-004]
```

```php
// Relationships to implement:
public function outlets(): HasMany       // → CustomerOutlet
public function purchaseOrders(): HasMany // → PurchaseOrder

// Fillable: nama, nama_perusahaan, alamat, payment_method
// Cast payment_method as string enum
```

---

### MODEL-002 — `CustomerOutlet` Model

```yaml
task_id: MODEL-002
type: model
file: app/Models/CustomerOutlet.php
depends_on: DB-002
```

```php
public function customer(): BelongsTo  // → Customer
// Fillable: customer_id, nama_outlet
```

---

### MODEL-003 — `Barang` Model

```yaml
task_id: MODEL-003
type: model
file: app/Models/Barang.php
depends_on: [DB-003, DB-005, DB-006]
```

```php
public function poItems(): HasMany    // → PoItem
public function hargaBelis(): HasMany // → HargaBeli
// Fillable: nama, satuan, harga_jual
```

---

### MODEL-004 — `PurchaseOrder` Model

```yaml
task_id: MODEL-004
type: model
file: app/Models/PurchaseOrder.php
depends_on: [DB-004, DB-005]
```

```php
public function customer(): BelongsTo        // → Customer
public function outlet(): BelongsTo          // → CustomerOutlet
public function items(): HasMany             // → PoItem

// Auto-generate no_po on creating event
// Format: PO-{6-digit padded sequence}
// Fillable: no_po, no_ref, customer_id, customer_outlet_id, tanggal, status
```

---

### MODEL-005 — `PoItem` Model

```yaml
task_id: MODEL-005
type: model
file: app/Models/PoItem.php
depends_on: [DB-005]
```

```php
public function purchaseOrder(): BelongsTo // → PurchaseOrder
public function barang(): BelongsTo        // → Barang
// Fillable: purchase_order_id, barang_id, qty
// Cast qty as decimal
```

---

### MODEL-006 — `HargaBeli` Model

```yaml
task_id: MODEL-006
type: model
file: app/Models/HargaBeli.php
depends_on: DB-006
```

```php
public function barang(): BelongsTo // → Barang
// Fillable: barang_id, tanggal, harga_beli
```

---

### MODEL-007 — `SuratJalan` Model

```yaml
task_id: MODEL-007
type: model
file: app/Models/SuratJalan.php
depends_on: DB-007
```

```php
public function customer(): BelongsTo // → Customer
public function outlet(): BelongsTo   // → CustomerOutlet
// Auto-generate no_sj: SJ-{6-digit padded sequence}
```

---

### MODEL-008 — `Invoice` Model

```yaml
task_id: MODEL-008
type: model
file: app/Models/Invoice.php
depends_on: DB-008
```

```php
public function customer(): BelongsTo // → Customer
// Auto-generate no_invoice: INV-{6-digit padded sequence}
// Fillable: no_invoice, customer_id, tanggal, total_tagihan, status
```

---

## BUSINESS WORKFLOWS

---

### WORKFLOW-01 — Purchase Order Creation

```yaml
task_id: WORKFLOW-01
controller: PurchaseOrderController
method: store
depends_on: [MODEL-001, MODEL-002, MODEL-003, MODEL-004, MODEL-005]
```

**Input:**
```yaml
required:
  - customer_id
  - customer_outlet_id
  - tanggal
  - no_ref
  - items: [{barang_id, qty}]
```

**Logic:**
1. Generate `no_po` (auto via model boot)
2. Create `PurchaseOrder` record with `status = baru`
3. Loop through `items`, create `PoItem` records
4. Return PO with items eager-loaded

**Acceptance Criteria:**
- [ ] PO visible in active order list
- [ ] `no_po` unique and formatted correctly
- [ ] All items saved with correct `qty` (decimal)

---

### WORKFLOW-02 — Daily Purchasing Consolidation

```yaml
task_id: WORKFLOW-02
controller: BelanjaController
method: konsolidasi
depends_on: [WORKFLOW-01, MODEL-006]
```

**Query Logic:**
```sql
SELECT
  b.id AS barang_id,
  b.nama,
  b.satuan,
  SUM(pi.qty) AS total_qty,
  -- outlet breakdown as JSON array
  JSON_ARRAYAGG(
    JSON_OBJECT(
      'outlet', co.nama_outlet,
      'qty', pi.qty
    )
  ) AS outlet_breakdown,
  hb.harga_beli
FROM po_items pi
JOIN purchase_orders po ON po.id = pi.purchase_order_id
JOIN barangs b ON b.id = pi.barang_id
JOIN customer_outlets co ON co.id = po.customer_outlet_id
LEFT JOIN harga_belis hb ON hb.barang_id = pi.barang_id AND hb.tanggal = CURDATE()
WHERE po.status IN ('baru', 'proses')
GROUP BY b.id, b.nama, b.satuan, hb.harga_beli
```

**Output:** Purchasing matrix — product, total qty, outlet breakdown, market purchase price

**Acceptance Criteria:**
- [ ] Returns consolidated quantities grouped by product
- [ ] Outlet breakdown visible per product
- [ ] Market price shown if already entered

---

### WORKFLOW-03 — Market Purchase Price Entry

```yaml
task_id: WORKFLOW-03
controller: BelanjaController
method: inputHarga
depends_on: [DB-006, MODEL-006]
```

**Logic:**
1. Accept `[{barang_id, harga_beli, tanggal}]`
2. Use `updateOrCreate(['barang_id', 'tanggal'], ['harga_beli' => ...])` per item

**Formula:**
```
Total Modal = SUM(qty × harga_beli)
```

**Acceptance Criteria:**
- [ ] Existing price for today overwritten (not duplicated)
- [ ] Cost updates immediately on consolidation view

---

### WORKFLOW-04 — Generate Delivery Order (Surat Jalan)

```yaml
task_id: WORKFLOW-04
controller: LogistikController
method: generateSJ
depends_on: [WORKFLOW-01, MODEL-007]
```

**Grouping:** `customer_id + customer_outlet_id`

**Logic:**
1. Find all PO with `status = baru` for given outlet
2. Create `SuratJalan` record
3. Auto-generate `no_sj`
4. Update PO status: `baru → proses`

**Print Options:**
- Toggle to show/hide selling price on printed SJ

**Acceptance Criteria:**
- [ ] SJ created per outlet
- [ ] PO status updated to `proses`
- [ ] SJ printable (A4, `@media print` hides sidebar/nav)
- [ ] Optional price visibility toggle works

---

### WORKFLOW-05 — Generate Invoice

```yaml
task_id: WORKFLOW-05
controller: InvoiceController
method: generate
depends_on: [WORKFLOW-04, MODEL-008]
```

**Source:** All PO with `status = proses`, grouped by `customer_id`

**Logic:**
1. Group PO items by customer
2. Calculate: `total_tagihan = SUM(qty × harga_jual)`
3. Create `Invoice` record with `status = terbit`
4. Update PO status: `proses → selesai`

**Acceptance Criteria:**
- [ ] Invoice total equals sum of all included PO values
- [ ] PO status changes to `selesai` after invoicing
- [ ] Invoice printable (A4 layout)

---

## CONTROLLERS

---

### CTRL-001 — `PurchaseOrderController`

```yaml
task_id: CTRL-001
file: app/Http/Controllers/PurchaseOrderController.php
```

| Method | Route | Description |
|---|---|---|
| `index` | GET /purchase-orders | List active POs |
| `create` | GET /purchase-orders/create | Form |
| `store` | POST /purchase-orders | Create PO + items |
| `show` | GET /purchase-orders/{id} | Detail |
| `edit` | GET /purchase-orders/{id}/edit | Edit form |
| `update` | PUT /purchase-orders/{id} | Update PO |
| `updateStatus` | PATCH /purchase-orders/{id}/status | Change status |

---

### CTRL-002 — `BelanjaController`

```yaml
task_id: CTRL-002
file: app/Http/Controllers/BelanjaController.php
```

| Method | Route | Description |
|---|---|---|
| `konsolidasi` | GET /belanja/konsolidasi | Consolidated purchasing list |
| `inputHarga` | POST /belanja/harga | Enter market prices |

---

### CTRL-003 — `LogistikController`

```yaml
task_id: CTRL-003
file: app/Http/Controllers/LogistikController.php
```

| Method | Route | Description |
|---|---|---|
| `index` | GET /logistik | Delivery orders list |
| `generate` | POST /logistik/generate | Create SJ from POs |
| `print` | GET /logistik/{id}/print | Printable SJ view |

---

### CTRL-004 — `InvoiceController`

```yaml
task_id: CTRL-004
file: app/Http/Controllers/InvoiceController.php
```

| Method | Route | Description |
|---|---|---|
| `index` | GET /invoices | Invoice list |
| `generate` | POST /invoices/generate | Generate from POs |
| `show` | GET /invoices/{id} | Invoice detail |
| `print` | GET /invoices/{id}/print | Printable invoice |
| `markLunas` | PATCH /invoices/{id}/lunas | Mark as paid |

---

### CTRL-005 — `FinanceReportController`

```yaml
task_id: CTRL-005
file: app/Http/Controllers/FinanceReportController.php
```

| Method | Route | Description |
|---|---|---|
| `dashboard` | GET /finance/dashboard | KPI + chart data |
| `priceTrend` | GET /finance/price-trend | Price volatility |
| `plReport` | GET /finance/pl | Profit & Loss report |
| `marginAnalysis` | GET /finance/margin | Margin per product |

---

## PHASE 2 — ANALYTICS & FINANCIAL INTELLIGENCE

---

### ANALYTICS-001 — Sales Dashboard KPIs

```yaml
task_id: ANALYTICS-001
controller: FinanceReportController@dashboard
depends_on: [WORKFLOW-05]
```

**KPI Formulas (source: completed POs only):**

```yaml
gross_revenue:
  formula: SUM(qty × harga_jual)
  source: po_items JOIN barangs WHERE po.status = 'selesai'

cogs:
  formula: SUM(qty × harga_beli)
  source: po_items JOIN harga_belis ON (barang_id, tanggal)

gross_profit:
  formula: gross_revenue - cogs

margin_pct:
  formula: (gross_profit / gross_revenue) × 100
```

---

### ANALYTICS-002 — Dashboard Charts

```yaml
task_id: ANALYTICS-002
depends_on: ANALYTICS-001
library: Chart.js
```

| Chart | Type | Grouping | Notes |
|---|---|---|---|
| Revenue vs COGS Trend | Line | Daily | Last 30 days default |
| Revenue Contribution | Doughnut | By customer | |
| Top Selling Products | Horizontal Bar | By qty sold | Top 5 only |
| Product Margin Ranking | Horizontal Bar | By margin % | |

---

### ANALYTICS-003 — Price Volatility Analysis

```yaml
task_id: ANALYTICS-003
controller: FinanceReportController@priceTrend
depends_on: DB-006
```

**Filter Options:** `4 weeks`, `8 weeks`, `12 weeks`

**Alert Rules:**

```yaml
danger:
  condition: price_increase_pct > 10
  window: 7 days
  example: "42000 → 48000"

warning:
  conditions:
    - price_increases_for: 3 consecutive weeks
    - margin_pct < 25
```

**Acceptance Criteria:**
- [ ] Alerts fire correctly per rule
- [ ] Chart renders per selected filter window

---

### ANALYTICS-004 — Profit & Loss Report

```yaml
task_id: ANALYTICS-004
controller: FinanceReportController@plReport
depends_on: [ANALYTICS-001, DB-009]
```

**P&L Structure:**

```
Revenue                    = SUM(qty × harga_jual)          [selesai POs]
- COGS                     = SUM(qty × harga_beli)
= Gross Profit             = Revenue - COGS
- Operating Expenses       = SUM(biaya_operasionals.jumlah)
  ├── Transport
  ├── Packaging
  ├── Komunikasi
  ├── Tak Terduga
  └── Lain-lain
= Net Profit               = Gross Profit - OPEX
  Net Margin %             = (Net Profit / Revenue) × 100
```

---

### ANALYTICS-005 — Break Even Point

```yaml
task_id: ANALYTICS-005
controller: FinanceReportController@dashboard
depends_on: ANALYTICS-004
```

**Formula:**
```
BEP Daily = Monthly OPEX / Active Operational Days
```

**Output:** Display daily minimum revenue target on dashboard

---

## UI REQUIREMENTS

---

### UI-001 — Design Theme

```yaml
task_id: UI-001
framework: Tailwind CSS
```

```yaml
colors:
  primary: Forest Green, Sage Green
  secondary: Amber, Slate Blue
```

---

### UI-002 — Responsive Tables

```yaml
task_id: UI-002
```

**All data tables must include:**
```html
<div class="overflow-x-auto">
  <table>...</table>
</div>
```

**Acceptance Criteria:**
- [ ] All tables scroll horizontally on tablet/mobile
- [ ] No content clipped on small screens

---

### UI-003 — Print Optimization

```yaml
task_id: UI-003
applies_to: [SuratJalan, Invoice]
```

**CSS Rules:**
```css
@media print {
  /* Hide */
  .sidebar, nav, .action-buttons { display: none; }

  /* Show centered A4 document */
  .print-document {
    width: 210mm;
    margin: 0 auto;
  }
}
```

**Acceptance Criteria:**
- [ ] Sidebar hidden on print
- [ ] Navigation hidden on print
- [ ] Action buttons hidden on print
- [ ] Document centered in A4 layout

---

## DEFINITION OF DONE

```yaml
checklist:
  database:
    - [ ] All 9 migrations created and run cleanly
    - [ ] All foreign keys and constraints enforced
    - [ ] All unique indexes in place

  models:
    - [ ] All 8 models created
    - [ ] All relationships implemented and tested
    - [ ] Auto-number generation working (PO, SJ, INV)

  workflows:
    - [ ] PO creation workflow operational
    - [ ] Daily purchasing consolidation returns correct aggregates
    - [ ] Market price entry uses updateOrCreate
    - [ ] Delivery order generation updates PO status correctly
    - [ ] Invoice generation calculates totals correctly

  controllers:
    - [ ] All 5 controllers implemented
    - [ ] All routes registered in web.php
    - [ ] Input validation on all store/update methods

  analytics:
    - [ ] Dashboard KPIs calculate correctly
    - [ ] All 4 Chart.js charts render
    - [ ] Price volatility alerts fire per rules
    - [ ] P&L report matches formula
    - [ ] BEP daily target displayed

  ui:
    - [ ] Green/amber theme applied consistently
    - [ ] All tables overflow-x-auto
    - [ ] Print layouts functional for SJ and Invoice
    - [ ] Mobile responsive (min 375px width)

  production:
    - [ ] .env configured for production
    - [ ] Laravel Breeze auth working
    - [ ] Application deployable on Linux VPS
```
