Bạn đang làm việc trong một plugin WordPress đặt phòng khách sạn.

## Mục tiêu

Dựng Database + Migration cho plugin dựa **1:1 theo đúng tài liệu KiotViet Hotel tôi đã cung cấp**.

Yêu cầu quan trọng nhất:

> Tên thuộc tính phải giữ nguyên đúng như tài liệu.
> Không được tự đổi tên.
> Không được tự chuẩn hóa camelCase thành snake_case.
> Không được tự thêm field rồi coi đó là field của KiotViet.
> Không được tự đoán schema nội bộ của KiotViet.

Ví dụ tài liệu ghi:

```text
branchId
customerId
checkInTime
checkOutTime
counterType
createdDate
modifiedDate
```

thì database cũng phải giữ:

```text
branchId
customerId
checkInTime
checkOutTime
counterType
createdDate
modifiedDate
```

TUYỆT ĐỐI không đổi thành:

```text
branch_id
customer_id
check_in_time
check_out_time
counter_type
created_at
updated_at
```

---

# 1. Nguyên tắc nguồn dữ liệu

Tài liệu tôi gửi là tài liệu API KiotViet Hotel.

Chỉ sử dụng những field được tài liệu mô tả.

Không được tuyên bố đây là database nội bộ thật của KiotViet.

Tên TABLE có thể do plugin tự định nghĩa vì tài liệu không cung cấp tên bảng DB.

Nhưng tên COLUMN lấy từ KiotViet phải giữ nguyên 100%.

Nếu plugin bắt buộc phải có thêm field phục vụ quan hệ DB hoặc WordPress, phải:

1. giải thích field đó không có trong tài liệu;
2. đánh dấu rõ là `PLUGIN EXTENSION`;
3. hỏi tôi trước khi thêm nếu nó ảnh hưởng schema chính.

---

# 2. WordPress convention

Tên bảng dùng prefix động:

```php
global $wpdb;

$table = $wpdb->prefix . 'hotel_orders';
```

Không hard-code:

```text
wp_hotel_orders
```

Sử dụng:

```php
$wpdb->get_charset_collate();
```

Ưu tiên migration bằng:

```php
dbDelta();
```

Không sửa WordPress core.

Không hard-code database name, domain hoặc absolute path.

---

# 3. Order / Booking

Tạo bảng đại diện cho object đặt phòng.

Tên bảng plugin có thể là:

```text
{prefix}hotel_orders
```

Các field lấy từ response KiotViet phải giữ đúng tên:

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

Không đổi tên bất kỳ field nào.

Booking status theo đúng tài liệu:

```text
1 = Đã đặt
2 = Hoàn thành
3 = Đã hủy
4 = Chưa xác nhận
```

Không tự tạo status khác nếu chưa hỏi tôi.

---

# 4. Request tạo đặt phòng

Tài liệu tạo booking có các field:

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

Trong đó:

```text
phone
```

required.

```text
customerName
email
```

optional.

```text
checkInTime
checkOutTime
```

required.

```text
counterType
```

required.

Theo tài liệu:

```text
1 = Giờ
2 = Ngày
3 = Đêm
```

Nếu response hoặc phần tài liệu khác có:

```text
4 = Tháng
```

thì giữ nguyên mapping đó tại nơi tương ứng.

Không tự hợp nhất hoặc thay đổi mapping.

---

# 5. Room Classes trong request

Tài liệu có:

```text
roomClasses
```

là danh sách.

Mỗi phần tử gồm đúng:

```text
id
quantity
price
note
version
```

Không đổi thành:

```text
roomClassId
room_class_id
basePrice
```

nếu tài liệu không ghi như vậy tại request này.

Nếu cần lưu dữ liệu request riêng, giữ nguyên tên:

```text
id
quantity
price
note
version
```

Quan hệ với bảng nào phải được giải thích riêng, không tự đổi field gốc.

---

# 6. Payment trong request

Payment theo đúng tài liệu:

```text
payment
{
    method
    amount
}
```

`method` hỗ trợ đúng:

```text
cash
card
transfer
```

Không tự thêm:

```text
transactionCode
status
paidAt
createdBy
updatedAt
```

nếu tài liệu tôi gửi không có.

Nếu plugin thực sự cần thêm các field đó thì phải đánh dấu:

```text
PLUGIN EXTENSION
```

và hỏi tôi trước.

---

# 7. Order Details

Tạo bảng plugin:

```text
{prefix}hotel_order_details
```

Các field xuất hiện trong tài liệu phải giữ nguyên:

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

Không đổi thành:

```text
bookingId
booking_id
room_id
parent_detail_id
extra_fee_id
check_in_time
base_price
sub_total
```

---

# 8. Order Detail Status

Theo đúng tài liệu:

```text
1 = Đã đặt
2 = Đã nhận phòng
3 = Đã trả phòng
4 = Đã hủy
5 = Chưa xác nhận
```

Không tự thêm status.

Không dùng text thay thế trong DB nếu đang bám tài liệu.

---

# 9. Product Type

Theo đúng tài liệu:

```text
2 = Hàng hóa
3 = Dịch vụ
6 = Hạng phòng
7 = Phụ phí
```

Giữ nguyên field:

```text
productType
```

Không đổi thành:

```text
product_type
```

Không đổi mã số.

---

# 10. Counter Type

Theo đúng tài liệu ở order detail:

```text
1 = Giờ
2 = Ngày
3 = Đêm
4 = Tháng
```

Field phải tên:

```text
counterType
```

Không đổi thành:

```text
counter_type
```

---

# 11. Parent relation

Tài liệu sử dụng:

```text
parentUuid
```

để liên kết detail con với detail cha.

Giữ nguyên:

```text
parentUuid
```

Không tự đổi thành:

```text
parentDetailId
parent_id
```

Nếu cần index thì index trực tiếp trên:

```text
parentUuid
```

---

# 12. Extra Fee relation

Tài liệu dùng:

```text
extraFeeId
```

Giữ nguyên tên.

Không đổi thành:

```text
extra_fee_id
```

Không tự tạo thêm cấu trúc phụ phí khác nếu tài liệu chưa mô tả.

---

# 13. Attachments

Tạo bảng plugin:

```text
{prefix}hotel_order_attachments
```

Các field đúng tài liệu:

```text
id
attachmentUrl
fileName
createdDate
```

Không đổi thành:

```text
attachment_url
file_name
created_at
```

Nếu cần liên kết attachment với order bằng:

```text
orderUuid
```

thì phải ghi rõ:

```text
orderUuid = PLUGIN EXTENSION nếu field này không nằm trong object attachment của tài liệu
```

Không được nói nó là field KiotViet nếu tài liệu attachment không ghi.

---

# 14. Sale Channels

Tạo bảng plugin:

```text
{prefix}hotel_sale_channels
```

Theo đúng tài liệu, giữ nguyên các field:

```text
id
name
isActive
createdBy
createdDate
Description
```

Đặc biệt:

```text
Description
```

phải giữ đúng capitalization như tài liệu nếu mục tiêu strict 1:1.

Không tự đổi:

```text
description
is_active
created_by
created_at
updated_at
```

---

# 15. Danh sách đặt phòng

API list hỗ trợ các tham số:

```text
createdDateFrom
createdDateTo
statuses
pageIndex
pageSize
```

Không đổi tên trong layer tương thích API.

Status filter theo đúng:

```text
1 = đã đặt
2 = hoàn thành
3 = đã hủy
4 = chưa xác nhận
```

`pageSize` theo tài liệu đặt phòng tối đa:

```text
50
```

Không tự thay đổi.

---

# 16. Sale Channel API filters

Giữ đúng tên request:

```text
name
isActive
saleChannelIds
pageIndex
pageSize
```

Theo tài liệu:

```text
pageSize mặc định 50
tối đa 100
```

Không đổi các tên này trong lớp DTO/API-compatible.

---

# 17. Data type

Phải chọn datatype dựa trên mô tả tài liệu.

Ví dụ:

```text
uuid
```

string/GUID phù hợp.

```text
customerId
branchId
retailerId
soldById
createdBy
modifiedBy
saleChannelId
productId
roomId
extraFeeId
orderId
```

dùng kiểu integer/big integer phù hợp.

Money:

```text
price
discount
total
totalPayment
surcharge
basePrice
subTotal
discountValue
amount
```

TUYỆT ĐỐI không dùng FLOAT.

Dùng:

```text
DECIMAL
```

phù hợp.

Boolean:

```text
isDeleted
isCompletedPayment
isActive
```

dùng kiểu phù hợp MySQL/WordPress, ví dụ:

```text
TINYINT(1)
```

nhưng tên column vẫn phải giữ nguyên.

Datetime:

```text
purchaseDate
modifiedDate
createdDate
checkInTime
checkOutTime
```

dùng DATETIME phù hợp.

Không đổi tên thành `_at`.

---

# 18. Nullable

Không tự gán `NOT NULL` cho tất cả field.

Dựa vào tài liệu:

```text
required
optional
```

để xác định nullable.

Ví dụ request:

```text
phone = required
customerName = optional
email = optional
checkInTime = required
checkOutTime = required
counterType = required
branchId = required
adultQuantity = optional
childQuantity = optional
note = optional
```

Nếu response không nói rõ nullable thì phải đánh dấu là quyết định implementation, không được giả vờ tài liệu đã xác nhận.

---

# 19. Index

Tạo index nhưng KHÔNG đổi tên column.

Ưu tiên các field phục vụ lookup:

```text
hotel_orders:
uuid
code
customerId
branchId
status
createdDate
purchaseDate
saleChannelId

hotel_order_details:
uuid
orderId
orderUuid
roomId
status
checkInTime
checkOutTime
productId
productType
branchId
parentUuid
extraFeeId

hotel_sale_channels:
id
name
isActive
```

Các unique constraint chỉ được áp dụng khi hợp lý.

Tối thiểu:

```text
uuid
code
```

có thể xem xét unique cho order.

Nếu tài liệu không đảm bảo uniqueness của field nào thì phải nêu rõ trước khi áp dụng.

---

# 20. Double booking

Đây là logic plugin, không phải field KiotViet.

Không tạo field mới chỉ để lưu trạng thái availability nếu không cần.

Kiểm tra overlap dựa trên:

```text
roomId
checkInTime
checkOutTime
status
```

Logic:

```text
requestedCheckIn < existingCheckOut
AND
requestedCheckOut > existingCheckIn
```

Không được chỉ kiểm tra frontend.

Đây là application logic, không được tuyên bố nó nằm trong tài liệu KiotViet nếu tài liệu không mô tả.

---

# 21. Không tự dựng các bảng ngoài tài liệu

Không được tự tạo ngay các bảng:

```text
hotel_branches
hotel_rooms
hotel_room_classes
hotel_prices
hotel_time_slots
hotel_promotions
hotel_customers
hotel_services
hotel_extra_fees
```

chỉ vì tài liệu có các field:

```text
branchId
roomId
roomClasses
productType
extraFeeId
```

Nếu tài liệu tôi cung cấp chưa mô tả object/API đầy đủ cho các entity trên thì:

1. không tự đoán field;
2. không tự dựng schema;
3. liệt kê chúng vào mục:

```text
CHƯA ĐỦ DỮ LIỆU TỪ TÀI LIỆU
```

và hỏi tôi cung cấp phần tài liệu tương ứng.

---

# 22. Không tự thêm WordPress fields vào schema KiotViet

Không tự thêm:

```text
wpUserId
created_at
updated_at
deleted_at
timeSlotId
promotionId
serviceId
roomClassId
bookingId
```

vào các bảng mirror KiotViet rồi coi chúng là field gốc.

Nếu cần:

```text
PLUGIN EXTENSION
```

phải tách riêng và ghi rõ.

---

# 23. Migration architecture

Plugin phải có DB version.

Ví dụ:

```text
HOTEL_BOOKING_DB_VERSION
```

Lưu:

```text
hotel_booking_db_version
```

bằng WordPress option.

Luồng:

```text
Plugin load
↓
Read installed DB version
↓
Compare target version
↓
Run missing migrations
↓
Only after success:
update DB version
```

Migration phải:

* không mất dữ liệu;
* không drop table khi update;
* không chạy lại vô hạn;
* không tạo duplicate column;
* không tạo dữ liệu demo;
* không xóa dữ liệu khi deactivate.

---

# 24. Activation

Khi activate plugin:

```text
check database version
→ create/update required tables
→ save version
```

Không tạo:

```text
demo room
demo booking
demo customer
demo payment
```

---

# 25. Deactivation

Deactivate không được:

```text
DROP TABLE
DELETE DATA
```

Dữ liệu phải còn nguyên.

---

# 26. Uninstall

Mặc định giữ dữ liệu.

Không tự xóa toàn bộ dữ liệu khi uninstall nếu chưa có setting/confirmation rõ ràng.

---

# 27. Kết quả phải trả trước khi code

Trước khi implement migration, hãy đưa cho tôi bảng mapping chính xác:

```text
SOURCE FIELD
SOURCE LOCATION
TYPE THEO TÀI LIỆU
REQUIRED / OPTIONAL
DB COLUMN
PLUGIN EXTENSION?
GHI CHÚ
```

Ví dụ:

```text
branchId
Order
int
required ở create request
branchId
No
Giữ nguyên tên
```

Hoặc:

```text
orderUuid
OrderDetail
guid
response
orderUuid
No
Giữ nguyên tên
```

Hoặc nếu cần field riêng:

```text
internalId
-
-
-
internalId
Yes
Không có trong KiotViet
```

---

# 28. Phải tự đối chiếu zero-rename

Trước khi code, chạy review thủ công toàn bộ schema.

Kiểm tra không có trường hợp:

```text
branchId → branch_id
customerId → customer_id
purchaseDate → purchase_date
createdDate → created_at
modifiedDate → updated_at
checkInTime → check_in_time
checkOutTime → check_out_time
roomId → room_id
productId → product_id
productType → product_type
counterType → counter_type
basePrice → base_price
subTotal → sub_total
totalPayment → total_payment
discountRatio → discount_ratio
saleChannelId → sale_channel_id
parentUuid → parent_uuid
extraFeeId → extra_fee_id
```

Nếu phát hiện bất kỳ rename nào phải sửa lại.

---

# 29. Không sửa lỗi/tên trong tài liệu âm thầm

Nếu tài liệu có:

```text
Description
```

thì giữ nguyên:

```text
Description
```

Nếu tài liệu có field hoặc wording trông không nhất quán, không tự sửa.

Hãy báo tôi:

```text
Tài liệu đang có điểm không nhất quán tại ...
```

và hỏi trước khi thay đổi.

---

# 30. Scope hiện tại

Chỉ dựng migration/schema từ những phần tài liệu đã được cung cấp:

```text
9. Đặt phòng
9.1 Tạo đặt phòng
9.2 Lấy chi tiết đặt phòng
9.3 Lấy danh sách đặt phòng
10. Kênh bán
10.1 Danh sách kênh bán
```

Không suy diễn các phần chưa có tài liệu.

---

# 31. Output cuối cùng

Sau khi hoàn thành, cung cấp:

```text
1. Danh sách table đã tạo
2. Danh sách column của từng table
3. Mapping từng column với field KiotViet
4. Field nào là PLUGIN EXTENSION
5. DB version
6. Migration files
7. Index
8. Những phần chưa thể dựng vì thiếu tài liệu
9. Kết quả zero-rename verification
```

Cuối cùng phải xác nhận rõ:

```text
Có bao nhiêu field lấy trực tiếp từ tài liệu.
Có bao nhiêu field plugin tự bổ sung.
Có field nào bị rename hay không.
```

Mục tiêu bắt buộc:

```text
KiotViet field rename count = 0
```

Nếu rename count khác 0 thì task chưa hoàn thành.

Nếu có bất kỳ điểm nào không chắc chắn, hỏi tôi bằng tiếng Việt trước khi tự quyết định.
