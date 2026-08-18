Bạn là Senior WordPress/PHP Developer.

Tôi đang xây dựng một plugin WordPress đặt phòng khách sạn.

Hãy đọc toàn bộ repository/plugin hiện tại trước khi code và triển khai tính năng theo đúng yêu cầu dưới đây.

# 1. Mục tiêu

Xây dựng một plugin WordPress Hotel Booking có thể đóng gói thành:

```text
hotel-booking.zip
```

và cài trên nhiều website WordPress khác nhau.

Plugin phải hoạt động độc lập, không phụ thuộc:

```text
theme cụ thể
domain cụ thể
database cụ thể
table prefix wp_
project hiện tại
absolute path
```

Phải dùng:

```php
$wpdb->prefix
```

và API chuẩn WordPress.

---

# 2. Giao diện booking mong muốn

Frontend cần dựng lịch đặt phòng dạng bảng tương tự giao diện mẫu tôi cung cấp.

Cấu trúc:

```text
Chi nhánh

                Sweet Heart                         Ball
          ─────────────────────            ─────────────────────

          08:00   11:20   14:40            08:20   11:40   15:00

Thứ   Ngày

T3    18/08     [ ] [★] [ ]               [ ] [ ] [★]

T4    19/08     [★] [ ] [ ]               [ ] [★] [ ]

T5    20/08     [ ] [ ] [★]               [ ] [ ] [ ]
```

Các cột phòng, ngày, giờ KHÔNG được hard-code.

Tất cả phải được lấy từ cấu hình do admin setup.

---

# 3. Trạng thái trên lịch

Frontend cần thể hiện ít nhất:

```text
Đã đặt
Đang chọn
Khuyến mại
Còn trống
Không khả dụng / đóng
```

Quy tắc:

```text
BOOKED
→ đã có booking giữ phòng

SELECTED
→ người dùng hiện tại vừa click chọn

PROMOTION
→ phòng còn trống và đang có khuyến mại

AVAILABLE
→ phòng còn trống

DISABLED
→ phòng hoặc khung giờ bị admin khóa
```

LƯU Ý:

```text
SELECTED
```

chỉ là frontend state.

Không lưu `SELECTED` vào database.

Availability phải được tính từ dữ liệu thật.

---

# 4. Admin Hotel Booking

Trong WordPress Admin tạo menu:

```text
Hotel Booking
├── Tổng quan
├── Chi nhánh
├── Hạng phòng
├── Phòng
├── Lịch / Khung giờ
├── Bảng giá
├── Đặt phòng
├── Khuyến mại
├── Kênh bán
└── Cài đặt
```

Không cần làm toàn bộ UI ngay một lúc.

Ưu tiên luồng lõi:

```text
Chi nhánh
↓
Hạng phòng
↓
Phòng
↓
Khung giờ
↓
Giá
↓
Calendar
↓
Booking
```

---

# 5. Admin setup phòng

Admin phải có khả năng tạo:

```text
Sweet Heart
Ball
Blue Sea
VIP
Deluxe
...
```

Phải phân biệt:

```text
Hạng phòng
```

và:

```text
Phòng vật lý
```

Ví dụ:

```text
Deluxe

├── 101
├── 102
└── 103
```

Không được hard-code phòng trong frontend.

---

# 6. Admin setup lịch và giờ

Admin phải tự cấu hình được khung giờ.

Ví dụ phòng/hạng phòng Sweet Heart:

```text
08:00 - 10:50
11:20 - 14:10
14:40 - 17:30
18:00 - 20:50
21:20 - 07:20
```

Ball có thể có lịch khác:

```text
08:20 - 11:10
11:40 - 14:30
15:00 - 17:50
18:20 - 21:10
21:40 - 07:40
```

Không được giả định tất cả phòng có cùng khung giờ.

Admin phải có khả năng:

```text
thêm khung giờ
sửa khung giờ
xóa/disable khung giờ
sắp xếp thứ tự
gán khung giờ cho hạng phòng/phòng
```

Có thể cấu hình theo:

```text
thứ trong tuần
ngày cụ thể
khoảng ngày
```

---

# 7. Counter Type

Phải giữ đúng field KiotViet:

```text
counterType
```

Không đổi thành:

```text
counter_type
```

Theo tài liệu:

```text
1 = Giờ
2 = Ngày
3 = Đêm
```

Ở Order Detail tài liệu còn mô tả:

```text
4 = Tháng
```

Do đó application phải hỗ trợ cấu trúc đủ mở cho:

```text
1 = Giờ
2 = Ngày
3 = Đêm
4 = Tháng
```

Không đổi mã.

---

# 8. Giá phòng

Admin phải setup được:

```text
giá mặc định
giá theo hạng phòng
giá theo khung giờ
giá theo thứ
giá theo khoảng ngày
giá đặc biệt
```

Ví dụ:

```text
Sweet Heart

T2 - T6
08:00 - 10:50
250.000đ

T7 - CN
08:00 - 10:50
350.000đ
```

Hoặc:

```text
20/08/2026
Sweet Heart
14:40 - 17:30

Giá thường:
300.000đ

Giá khuyến mại:
250.000đ
```

Không lưu giá trực tiếp vào calendar cell.

Calendar phải resolve giá động.

---

# 9. Khi click vào một ô lịch

Ví dụ khách click:

```text
Sweet Heart
20/08/2026
08:00 - 10:50
```

Frontend phải lấy đúng:

```text
branch
room / room class
date
checkInTime
checkOutTime
counterType
price
promotion
availability
```

Sau đó hiện popup/card:

```text
Sweet Heart

20/08/2026
08:00 - 10:50

Giá:
250.000đ

Khuyến mại:
200.000đ

[Đặt phòng]
```

Chỉ được hiển thị nút đặt nếu slot còn khả dụng.

---

# 10. Bấm Đặt phòng

Khi bấm:

```text
Đặt phòng
```

phải chuyển sang trang phòng tương ứng.

Ví dụ:

```text
/phong/sweet-heart/
```

và giữ context booking:

```text
/phong/sweet-heart/
?date=2026-08-20
&checkInTime=2026-08-20T08:00
&checkOutTime=2026-08-20T10:50
```

Không phụ thuộc vào query format này nếu codebase đã có convention tốt hơn, nhưng phải giữ được toàn bộ booking context.

Trang phòng hiển thị:

```text
Tên phòng

Ảnh
Mô tả

Ngày
Giờ nhận
Giờ trả

Giá gốc
Khuyến mại
Tổng tiền

Tên khách
SĐT
Email

Số người lớn
Số trẻ em

Ghi chú

[ĐẶT PHÒNG]
```

---

# 11. DATABASE KIOTVIET — QUY TẮC TUYỆT ĐỐI

Database booking phải bám theo tài liệu KiotViet tôi cung cấp.

TÊN FIELD KIOTVIET PHẢI GIỮ NGUYÊN 100%.

Ví dụ:

```text
branchId
```

phải luôn là:

```text
branchId
```

TUYỆT ĐỐI không đổi thành:

```text
branch_id
```

Tương tự:

```text
customerId
purchaseDate
createdDate
modifiedDate
checkInTime
checkOutTime
counterType
roomId
roomName
productId
productType
basePrice
subTotal
orderUuid
parentUuid
extraFeeId
saleChannelId
```

không được chuyển sang snake_case.

Mục tiêu:

```text
KiotViet field rename count = 0
```

---

# 12. Không được giả mạo schema KiotViet

Tài liệu tôi cung cấp là tài liệu API KiotViet Hotel, không phải database schema nội bộ.

Vì vậy:

```text
Tên TABLE
→ plugin được phép tự thiết kế

Tên COLUMN lấy từ API KiotViet
→ phải giữ nguyên 100%
```

Không được nói:

```text
"đây là database nội bộ của KiotViet"
```

Nếu tài liệu không cung cấp schema một entity thì ghi:

```text
PLUGIN EXTENSION
```

hoặc:

```text
CHƯA ĐỦ DỮ LIỆU KIOTVIET
```

Không tự suy diễn rồi gắn nhãn KiotViet.

---

# 13. Booking request — giữ nguyên KiotViet

Request tạo booking có:

```text
phone
customerName
email
checkInTime
checkOutTime
counterType
branchId
adultQuantity
childQuantity
note
roomClasses
payment
```

Giữ nguyên tên.

Không đổi:

```text
customerName → customer_name
checkInTime → check_in_time
checkOutTime → check_out_time
adultQuantity → adult_quantity
```

---

# 14. Room Classes request

`roomClasses` chứa:

```text
id
quantity
price
note
version
```

Giữ nguyên đúng các field.

Không tự đổi:

```text
id
```

thành:

```text
roomClassId
```

trong layer tương thích KiotViet nếu tài liệu request không ghi như vậy.

---

# 15. Payment

Theo tài liệu:

```text
payment
{
    method
    amount
}
```

`method`:

```text
cash
card
transfer
```

Giữ nguyên.

Không tự thêm các field như:

```text
transactionCode
gateway
paidAt
```

vào KiotViet model.

Nếu plugin cần thì phải ghi rõ:

```text
PLUGIN EXTENSION
```

---

# 16. Booking / Order

Tạo bảng plugin, ví dụ:

```text
{prefix}hotel_orders
```

Các field KiotViet phải có đúng tên:

```text
uuid
customerId
purchaseDate
code
branchId
status
modifiedDate
retailerId
discount
soldById
createdDate
createdBy
createdByName
modifiedBy
discountRatio
total
totalPayment
surcharge
saleChannelId
originVersion
historyNote
isCompletedPayment
adultQuantity
childQuantity
subTotal
discountValue
```

Không đổi tên.

Order status:

```text
1 = Đã đặt
2 = Hoàn thành
3 = Đã hủy
4 = Chưa xác nhận
```

Không đổi mapping.

---

# 17. Order Detail

Tạo:

```text
{prefix}hotel_order_details
```

Giữ nguyên các field xuất hiện trong tài liệu:

```text
id
orderId
uuid
productId
quantity
price
discount
createdDate
discountRatio
modifiedDate
orderUuid
isDeleted
roomId
roomName
checkInTime
checkOutTime
status
productType
counterType
retailerId
branchId
basePrice
subTotal
parentUuid
extraFeeId
```

Không rename.

---

# 18. Order Detail status

Giữ đúng:

```text
1 = Đã đặt
2 = Đã nhận phòng
3 = Đã trả phòng
4 = Đã hủy
5 = Chưa xác nhận
```

Calendar phải dựa vào các trạng thái này để xác định slot có giữ phòng hay không.

---

# 19. Product Type

Giữ đúng field:

```text
productType
```

Mapping:

```text
2 = Hàng hóa
3 = Dịch vụ
6 = Hạng phòng
7 = Phụ phí
```

Không đổi số.

---

# 20. Attachment

Nếu triển khai attachment thì tạo:

```text
{prefix}hotel_order_attachments
```

Field KiotViet:

```text
id
attachmentUrl
fileName
createdDate
```

Giữ nguyên.

Nếu cần thêm:

```text
orderUuid
```

để relational DB của plugin liên kết attachment với order, phải đánh dấu:

```text
PLUGIN EXTENSION
```

nếu object attachment trong tài liệu không chứa field này.

---

# 21. Sale Channel

Tạo:

```text
{prefix}hotel_sale_channels
```

Giữ nguyên field tài liệu:

```text
id
name
isActive
createdBy
createdDate
Description
```

Không đổi:

```text
isActive → is_active
createdBy → created_by
createdDate → created_at
Description → description
```

Booking sử dụng:

```text
saleChannelId
```

để liên kết.

---

# 22. Plugin extension database

Các tính năng frontend tôi yêu cầu như:

```text
setup phòng
setup lịch
setup giờ
giá
khuyến mại
availability
```

không được tài liệu KiotViet hiện tại mô tả đầy đủ schema.

Do đó có thể thiết kế các bảng riêng của plugin.

Ví dụ:

```text
{prefix}hotel_plugin_branches
{prefix}hotel_plugin_room_classes
{prefix}hotel_plugin_rooms
{prefix}hotel_plugin_schedules
{prefix}hotel_plugin_prices
{prefix}hotel_plugin_promotions
```

Nhưng phải ghi rõ:

```text
PLUGIN EXTENSION
```

Các bảng này phục vụ UI/plugin và không được tuyên bố là schema KiotViet.

---

# 23. Quan hệ giữa Plugin Extension và KiotViet data

Thiết kế phải hướng tới:

```text
PLUGIN ROOM
      │
      ↓
KiotViet-compatible roomId

PLUGIN ROOM CLASS
      │
      ↓
KiotViet-compatible productId / roomClasses.id

PLUGIN BRANCH
      │
      ↓
branchId
```

Không duplicate dữ liệu vô lý.

Nếu cần một mapping field riêng, phải mô tả rõ.

---

# 24. Availability

Không tạo một row database cho từng:

```text
room
+
date
+
time slot
```

trong toàn bộ tương lai.

Không tạo kiểu:

```text
hotel_calendar_cells
```

để pre-generate hàng triệu cell.

Calendar được tính động từ:

```text
Room
+
Schedule
+
Price
+
Promotion
+
Order Detail
```

---

# 25. Booking conflict

Trước khi tạo booking phải check backend.

Không được chỉ disable bằng JavaScript.

Sử dụng:

```text
roomId
checkInTime
checkOutTime
status
```

Kiểm tra overlap:

```text
requestedCheckIn < existingCheckOut

AND

requestedCheckOut > existingCheckIn
```

Ví dụ:

```text
Booking hiện tại:
10:00 → 12:00

Booking mới:
11:00 → 13:00
```

→ KHÔNG được đặt.

Nhưng:

```text
10:00 → 12:00
12:00 → 14:00
```

→ được phép nếu hệ thống sử dụng interval:

```text
[checkInTime, checkOutTime)
```

---

# 26. Race condition

Phải xử lý tình huống:

```text
User A
và
User B

cùng mở một slot còn trống.
```

Cả hai cùng click:

```text
Đặt phòng
```

Không được để tạo 2 booking cho cùng một phòng và khoảng giờ.

Frontend check availability chỉ để UX.

Backend phải check lại ngay trước khi insert.

Nếu cần transaction/locking phải triển khai theo cách tương thích MySQL/WordPress hiện tại.

---

# 27. Khuyến mại

Admin có thể cấu hình:

```text
Tên khuyến mại

Phòng / hạng phòng

Ngày bắt đầu
Ngày kết thúc

Giờ áp dụng

Thứ áp dụng

Loại giảm:
- %
- số tiền

Giá trị giảm

Trạng thái
```

Ví dụ:

```text
Sweet Heart

20/08 - 30/08

14:40 - 17:30

Giảm:
20%
```

Calendar:

```text
slot còn trống
+
promotion match
```

→ hiển thị trạng thái:

```text
PROMOTION
```

Không đổi booking status thành `PROMOTION`.

Promotion là pricing/UI state, không phải order status.

---

# 28. Click promotion

Khi click vào slot promotion:

```text
Giá gốc:
300.000đ

Giảm:
20%

Giá cuối:
240.000đ
```

Giá cuối cùng phải được backend tính lại khi tạo order.

Không tin giá gửi từ frontend.

Frontend chỉ gửi booking selection.

Backend tự resolve giá thật.

---

# 29. WordPress User / Customer

Không xây password database riêng.

Sử dụng authentication của WordPress:

```text
wp_users
wp_usermeta
```

Nếu khách login:

```text
WordPress User
↓
Customer/Booking
```

Nếu cho phép guest booking:

```text
phone
customerName
email
```

được lấy trực tiếp từ form booking.

Không bắt buộc user phải có tài khoản nếu setting cho phép guest booking.

---

# 30. Trang frontend của plugin

Plugin cần hỗ trợ:

```text
/dat-phong/
/phong/{slug}/
/dang-nhap/
/tai-khoan/
```

Có thể sử dụng:

```text
shortcode
Custom Post Type
rewrite rule
template override
```

tùy convention WordPress hiện tại.

Ưu tiên giải pháp portable nhất.

---

# 31. Shortcode

Có thể cung cấp:

```text
[hotel_booking_calendar]
```

để admin đặt lịch booking vào bất kỳ Page nào.

Ví dụ:

```text
Page: Đặt phòng

[hotel_booking_calendar]
```

Sau đó site có:

```text
https://domain.com/dat-phong/
```

Plugin phải render bên trong theme hiện tại.

Không thay toàn bộ theme.

---

# 32. Responsive

Giao diện calendar desktop tương tự ảnh tôi cung cấp.

Do bảng có nhiều phòng và nhiều giờ, desktop cho phép:

```text
horizontal scroll
```

Mobile không được ép toàn bộ bảng nhỏ lại đến mức không đọc được.

Mobile có thể:

```text
scroll ngang

hoặc

switch room selector
+
date cards
```

nhưng không được phá logic booking.

---

# 33. Security

Tất cả admin actions phải có:

```text
capability check
nonce
sanitize
validate
escape
```

Tất cả query nhận input phải dùng:

```php
$wpdb->prepare()
```

Không concat trực tiếp user input vào SQL.

Frontend không được có quyền:

```text
tự gửi price
tự gửi status
tự set discount
tự set total
```

Backend phải calculate lại.

---

# 34. Migration

Plugin phải có database migration version.

Ví dụ:

```text
HOTEL_BOOKING_DB_VERSION
```

WordPress option:

```text
hotel_booking_db_version
```

Migration phải:

```text
idempotent
không mất dữ liệu
không drop table bừa
không chạy lại mỗi request
không tạo demo data
```

Activate:

```text
check version
↓
run missing migrations
↓
update version
```

Deactivate:

```text
KHÔNG DELETE DATA
KHÔNG DROP TABLE
```

---

# 35. Data type

Money:

```text
DECIMAL
```

Không dùng:

```text
FLOAT
```

Date/time:

```text
DATETIME
DATE
TIME
```

tùy đúng bản chất dữ liệu.

Boolean:

```text
TINYINT(1)
```

nếu phù hợp.

ID cần tương thích WordPress/MySQL.

---

# 36. Index quan trọng

KiotViet-compatible Order:

```text
uuid
code
customerId
branchId
status
createdDate
purchaseDate
saleChannelId
```

Order Detail:

```text
uuid
orderUuid
orderId
roomId
status
checkInTime
checkOutTime
productId
productType
counterType
branchId
parentUuid
extraFeeId
```

Đặc biệt cần index phục vụ availability:

```text
roomId
status
checkInTime
checkOutTime
```

Không index mọi column một cách máy móc.

---

# 37. Những gì KHÔNG được làm

Không:

```text
đổi tên field KiotViet
hard-code phòng
hard-code giờ
hard-code giá
hard-code wp_
hard-code domain

tạo calendar cell vô hạn

tin price frontend

chỉ check booking conflict bằng JS

xóa DB khi deactivate

tự đoán field KiotViet chưa có tài liệu

sửa phần không liên quan
```

---

# 38. Phase triển khai

Không làm tất cả một lượt.

## Phase 1

Database + migration:

```text
KiotViet-compatible order
KiotViet-compatible order detail
sale channel
plugin branch
plugin room class
plugin room
plugin schedule
plugin price
plugin promotion
```

## Phase 2

Admin:

```text
Branch
Room Class
Room
Schedule
Price
Promotion
```

## Phase 3

Frontend calendar:

```text
rooms
+
days
+
time slots
+
availability
+
price
+
promotion
```

## Phase 4

Room detail:

```text
click slot
→ popup price
→ Đặt phòng
→ room page
```

## Phase 5

Create booking:

```text
validate
↓
resolve price
↓
check availability
↓
check conflict
↓
create order
↓
create order details
```

## Phase 6

Quản trị booking:

```text
Đã đặt
Chưa xác nhận
Đã nhận phòng
Đã trả phòng
Đã hủy
```

---

# 39. Trước khi code

Trước khi thay đổi source code, trả tôi:

### A. KiotViet mapping

```text
Field
Source
Type
Required/Optional
Database column
Renamed?
```

Yêu cầu:

```text
Renamed = NO
```

cho toàn bộ field KiotViet.

### B. Plugin extension schema

Liệt kê riêng:

```text
table
field
purpose
relation với KiotViet
```

Không trộn với KiotViet schema.

### C. ERD

Mô tả quan hệ:

```text
Branch
↓
Room Class
↓
Room
↓
Schedule

Room Class
↓
Price
↓
Promotion

Order
↓
Order Details
↓
Room

Order
↓
Sale Channel
```

### D. Files sẽ sửa/tạo

Phải dựa trên repository thực tế.

Không tự đoán path.

### E. Các điểm chưa rõ

Nếu tài liệu chưa đủ hoặc cần quyết định kiến trúc, hỏi tôi bằng tiếng Việt.

---

# 40. Verify bắt buộc

Sau khi code phải test:

```text
Fresh WordPress
→ install plugin
→ activate
→ migration success
```

Test prefix:

```text
abc_
```

thay vì chỉ:

```text
wp_
```

Test:

```text
Admin tạo phòng
Admin tạo giờ
Admin tạo giá
Admin tạo promotion

→ frontend hiển thị đúng
```

Test booking:

```text
available
→ đặt được

booked
→ không đặt được

promotion
→ đúng giá

cancelled booking
→ phòng trở lại available
```

Test conflict:

```text
10:00 - 12:00 existing

11:00 - 13:00 new
→ reject
```

Test:

```text
10:00 - 12:00 existing

12:00 - 14:00 new
→ accept
```

nếu interval `[start,end)`.

---

# 41. Zero rename verification

Sau khi hoàn thành phải rà toàn bộ KiotViet fields:

```text
branchId
customerId
purchaseDate
createdDate
modifiedDate
checkInTime
checkOutTime
counterType
roomId
roomName
productId
productType
basePrice
subTotal
totalPayment
discountRatio
saleChannelId
parentUuid
extraFeeId
orderUuid
isDeleted
isCompletedPayment
adultQuantity
childQuantity
discountValue
```

Không được có mapping kiểu:

```text
branchId → branch_id
checkInTime → check_in_time
createdDate → created_at
```

Kết quả cuối:

```text
KiotViet rename count = 0
```

Nếu khác `0` thì task chưa hoàn thành.

---

# 42. Nguyên tắc làm việc

Clean code.

Bám sát codebase hiện có.

Không over-engineer.

Không tự ý sửa database/schema khác ngoài phạm vi task.

Không tự ý thay đổi field KiotViet.

Không hiểu thì hỏi.

Nếu cần hỏi tôi bất kỳ điều gì, phải hỏi bằng tiếng Việt.

Commit message nếu cần:

```text
feat(hotel-booking): ...
```

hoặc:

```text
fix(hotel-booking): ...
```

Nội dung commit viết bằng tiếng Việt.
