<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <title>มีรถหรือยัง?</title>
    <link rel="stylesheet" href="Ustyle.css">
</head>
<body>

    <?php include 'Uheader.php'; ?>
    
    <main class="container">
        
        <div class="payment-layout">
            
            <div class="payment-left">
                <div class="cart-label">ตะกร้าสินค้า</div>
                
                <div class="cart-item-box">
                    <div class="c-col c-img">
                        <div class="img-placeholder">รูปภาพ</div>
                    </div>
                    <div class="c-col c-detail">รายละเอียด</div>
                    <div class="c-col c-qty">จำนวน</div>
                    <div class="c-col c-price">ราคาต่อหน่วย</div>
                </div>

                <div class="booking-form-box">
                    <div class="form-row">
                        <label>จำนวนวันที่จอง</label>
                        <select class="input-field">
                            <option>1</option>
                            <option>2</option>
                            <option>3</option>
                        </select>
                    </div>
                    <div class="form-row">
                        <label>อัปโหลดเอกสารสำคัญ<br>(ใบขับขี่และบัตรประชาชน)</label>
                        <div class="file-upload-fake">อัพโหลดไฟล์</div>
                    </div>
                </div>
            </div>

            <div class="payment-right">
                
                <div class="pay-method-box">
                    <h3>การชำระเงิน</h3>
                    
                    <div class="method-option">
                        <input type="radio" name="paymethod" id="bank" checked>
                        <div class="method-detail">
                            <label for="bank">โอนเข้าธนาคาร</label>
                            <div class="bank-icons">
                                <span class="icon-mock blue"></span>
                                <span class="icon-mock yellow"></span>
                                <span class="icon-mock green"></span>
                                <span class="icon-mock purple"></span>
                            </div>
                        </div>
                    </div>

                    <div class="method-option">
                        <input type="radio" name="paymethod" id="credit">
                        <div class="method-detail">
                            <label for="credit">บัตรเครดิต</label>
                            <div class="bank-icons">
                                <span class="icon-mock visa">VISA</span>
                                <span class="icon-mock master">Master</span>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="summary-box">
                    <div class="sum-row">
                        <span>ชื่อรถ</span>
                        <span>0.00฿</span>
                    </div>
                    <div class="sum-row">
                        <span>รวมราคาทั้งสิ้น</span>
                        <span>0.00฿</span>
                    </div>

                    <div class="action-buttons-pay">
                        <a href="cushistory.html" class="btn-pay-confirm" style="text-decoration:none; display:inline-block; text-align:center;">ชำระเงิน</a>
                        <a href="cardetail.html" class="btn-cancel" style="text-decoration:none; display:inline-block; text-align:center;">ยกเลิก</a>
                    </div>
                </div>

            </div>
        </div>

</html>