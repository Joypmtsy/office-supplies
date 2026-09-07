<div class="modal-backdrop" id="itemModalBackdrop" onclick="if(event.target===this) closeModal();">
  <div class="item-modal" role="dialog" aria-modal="true" aria-labelledby="modalItemName">
    <button type="button" class="item-modal__close" onclick="closeModal()" aria-label="ยกเลิก / ปิด">✕</button>

    <div class="item-modal__inner">
      <div class="item-modal__top">
        <div class="item-modal__imgbox" onclick="document.getElementById('edImage').click()" tabindex="0"
             role="button" aria-label="เปลี่ยนรูปภาพ"
             onkeydown="if(event.key==='Enter'||event.key===' '){event.preventDefault();document.getElementById('edImage').click();}">
          <img id="edPreview" class="item-modal__img" src="assets/images/default.png" alt="รูปภาพรายการ">
          <div class="item-modal__imgHint">🖼️ รูปภาพ<br><small>คลิกเพื่อเพิ่ม/เปลี่ยนรูป</small></div>
        </div>
        <input type="file" id="edImage" name="image" form="editDetailsForm"
               accept="image/jpeg,image/png,image/webp,image/gif" style="display:none" onchange="previewImage(this)">

        <div class="item-modal__infobox">
          <div class="item-modal__name" id="modalItemName">ชื่อรายการ</div>
          <div class="item-modal__stockline">
            สต็อกปัจจุบัน <b id="modalStockVal">0</b> <span id="modalStockUnit"></span>
          </div>
          <div class="item-modal__statusrow">
            <span class="item-modal__packnote" id="modalPackNote"></span>
            <span id="modalCurrentBadge"></span>
          </div>
        </div>
      </div>

      <div class="item-modal__navrow">
        <button type="button" class="item-modal__navbtn" onclick="navigateModalItem(-1)" aria-label="รายการก่อนหน้า">‹</button>
        <div class="modal-tabs">
          <button type="button" class="modal-tab-btn edit-tab active" onclick="switchModalTab('edit')">แก้ไขข้อมูล</button>
          <button type="button" class="modal-tab-btn stock-in-tab" onclick="switchModalTab('stock-in')">เติมสต็อก</button>
          <button type="button" class="modal-tab-btn stock-out-tab" onclick="switchModalTab('stock-out')">เบิกอุปกรณ์</button>
        </div>
        <button type="button" class="item-modal__navbtn" onclick="navigateModalItem(1)" aria-label="รายการถัดไป">›</button>
      </div>

      <div class="item-modal__body">
        <div class="modal-tab-content active" id="tab-edit">
          <form id="editDetailsForm" onsubmit="submitEditDetails(event)" enctype="multipart/form-data">
            <input type="hidden" name="item_id" id="edItemId">
            <div class="form-group">
              <label for="edGroup">ชื่อกลุ่ม</label>
              <input type="text" id="edGroup" name="group_name" required>
            </div>
            <div class="form-group">
              <label for="edName">ชื่อรายการ</label>
              <input type="text" id="edName" name="name" required>
            </div>
            <div class="form-group">
              <label for="edUnit">หน่วย</label>
              <input type="text" id="edUnit" name="unit" required>
            </div>
            <div class="form-group">
              <label for="edPackQty">จำนวนต่อแพ็ค (ถ้ามี)</label>
              <input type="number" id="edPackQty" name="pack_qty" min="1" placeholder="เช่น 12 (ไม่บังคับ)">
              <div class="hint">แสดงเป็น "1 แพ็ค / N หน่วย" ใต้ชื่อรายการ เว้นว่างได้ถ้ารายการนี้ไม่มีแพ็ค</div>
            </div>
            <button type="submit" class="btn item-modal__submit" style="width:100%;">💾 บันทึกการแก้ไข</button>
          </form>
        </div>

        <div class="modal-tab-content" id="tab-stock-in">
          <form id="stockInForm" onsubmit="submitStockIn(event)">
            <input type="hidden" name="item_id" id="siItemId">
            <div class="form-group">
              <label for="siQty">จำนวนที่เติม</label>
              <div class="qty-slider-row">
                <input type="number" id="siQtyNum" class="qty-slider-num" min="1" value="1" oninput="onSiQtyNumInput()">
                <input type="range" id="siQty" name="quantity" min="1" max="1000" value="1" oninput="onSiQtySliderInput()">
                <span class="qty-slider-max" id="siMaxLabel">1000</span>
              </div>
            </div>
            <div class="item-modal__formgrid">
              <div class="qf-col">
                <div class="qf-row">
                  <label for="siRequester">ผู้เติม</label>
                  <input type="text" id="siRequester" name="requester" required placeholder="ชื่อผู้รับผิดชอบ">
                </div>
                <div class="qf-row">
                  <label for="siDate">วันที่เติม</label>
                  <input type="date" id="siDate" name="txn_date" required>
                </div>
              </div>
              <div class="qf-note-col">
                <label for="siNote">หมายเหตุ:</label>
                <textarea id="siNote" name="note" rows="4" placeholder="หมายเหตุเพิ่มเติม"></textarea>
              </div>
            </div>
            <button type="submit" class="btn item-modal__submit" style="width:100%;">💾 บันทึกการเติมสต็อก</button>
          </form>
        </div>

        <div class="modal-tab-content" id="tab-stock-out">
          <form id="stockOutForm" onsubmit="submitStockOut(event)">
            <input type="hidden" name="item_id" id="soItemId">
            <div class="form-group">
              <label>จำนวนการเบิก</label>
              <div class="qty-slider-row">
                <input type="number" id="soQtyNum" class="qty-slider-num" min="1" max="1" value="1" oninput="onQtyNumInput()">
                <input type="range" id="soQty" name="quantity" min="1" max="1" value="1" oninput="onQtySliderInput()">
                <span class="qty-slider-max" id="soMaxLabel">0</span>
              </div>
            </div>
            <div class="item-modal__formgrid">
              <div class="qf-col">
                <div class="qf-row">
                  <label for="soRequester">ชื่อผู้เบิก</label>
                  <input type="text" id="soRequester" name="requester" required placeholder="ชื่อผู้เบิก">
                </div>
                <div class="qf-row">
                  <label for="soDept">แผนก</label>
                  <select id="soDept" name="department_id" required>
                    <option value="">-- เลือกแผนก --</option>
                    <?php foreach ($departments as $dept): ?>
                    <option value="<?= (int)$dept['id'] ?>"><?= htmlspecialchars($dept['name']) ?></option>
                    <?php endforeach; ?>
                  </select>
                </div>
                <div class="qf-row">
                  <label for="soDate">วันที่เบิก</label>
                  <input type="date" id="soDate" name="txn_date" required>
                </div>
              </div>
              <div class="qf-note-col">
                <label for="soNote">หมายเหตุ:</label>
                <textarea id="soNote" name="note" rows="4" placeholder="หมายเหตุเพิ่มเติม"></textarea>
              </div>
            </div>
            <button type="submit" class="btn item-modal__submit" style="width:100%;">💾 บันทึกการเบิกอุปกรณ์</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="retro-modal-backdrop" id="deleteConfirmBackdrop" onclick="if(event.target===this) cancelDeleteConfirm();">
  <div class="retro-modal" role="alertdialog" aria-modal="true" aria-labelledby="deleteConfirmTitle">
    <div class="retro-modal__titlebar">
      <span class="retro-modal__title" id="deleteConfirmTitle">DELETE</span>
      <button type="button" class="retro-modal__close" onclick="cancelDeleteConfirm()" aria-label="ยกเลิก">✕</button>
    </div>
    <div class="retro-modal__body">
      <p class="retro-modal__text" id="deleteConfirmText"></p>
      <button type="button" class="retro-modal__yes" id="deleteConfirmYesBtn" onclick="confirmDeleteYes()">YES</button>
    </div>
  </div>
</div>

<div class="modal-backdrop" id="addModalBackdrop" onclick="if(event.target===this) closeAddModal();">
  <div class="modal" role="dialog" aria-modal="true" aria-labelledby="addModalTitle">
    <div class="modal__header">
      <div class="modal__title" id="addModalTitle">
        <span>➕</span>
        <span>เพิ่มรายการใหม่</span>
      </div>
      <button type="button" class="modal__close" onclick="closeAddModal()" aria-label="ปิด">✕</button>
    </div>
    <div class="modal__body">
      <form id="addItemForm" onsubmit="submitAddItem(event)" enctype="multipart/form-data">
        <div class="form-group">
          <label>รูปภาพ (ถ้ามี)</label>
          <div class="img-preview-wrap">
            <img id="addPreview" class="img-preview" src="assets/images/default.png" alt="preview">
            <input type="file" id="addImage" name="image" accept="image/jpeg,image/png,image/webp,image/gif" onchange="previewAddImage(this)">
          </div>
          <div class="hint">รองรับ JPG, PNG, WEBP, GIF ขนาดไม่เกิน 2MB (ไม่เลือกไฟล์ = ใช้รูป default)</div>
        </div>
        <div class="form-group">
          <label for="addGroup">ชื่อกลุ่ม</label>
          <input type="text" id="addGroup" name="group_name" required placeholder="เช่น ปากกา, กระดาษ A4" list="groupNameList">
          <datalist id="groupNameList"></datalist>
        </div>
        <div class="form-group">
          <label for="addName">ชื่อรายการ</label>
          <input type="text" id="addName" name="name" required placeholder="ชื่อเต็มของรายการ">
        </div>
        <div class="form-group">
          <label for="addUnit">หน่วย</label>
          <input type="text" id="addUnit" name="unit" required placeholder="เช่น ด้าม, ชิ้น, รีม">
        </div>
        <div class="form-group">
          <label for="addPackQty">จำนวนต่อแพ็ค (ถ้ามี)</label>
          <input type="number" id="addPackQty" name="pack_qty" min="1" placeholder="เช่น 12 (ไม่บังคับ)">
          <div class="hint">แสดงเป็น "1 แพ็ค / N หน่วย" ใต้ชื่อรายการ เว้นว่างได้ถ้ารายการนี้ไม่มีแพ็ค</div>
        </div>
        <div class="form-group">
          <label for="addStock">จำนวนสต็อกเริ่มต้น</label>
          <input type="number" id="addStock" name="current_stock" min="0" value="0" required>
        </div>
        <div class="form-group">
          <label for="addMinStock">สต็อกขั้นต่ำ (สำหรับแจ้งเตือนใกล้หมด)</label>
          <input type="number" id="addMinStock" name="min_stock" min="0" value="5" required>
        </div>
        <button type="submit" class="btn btn-primary" style="width:100%;">💾 เพิ่มรายการ</button>
      </form>
    </div>
  </div>
</div>