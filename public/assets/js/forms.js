// Forms dynamics
function addSiblingRow() {
    var tbody = document.getElementById("siblingsTableBody");
    if (!tbody) return;
    var count = tbody.children.length;
    var tr = document.createElement("tr");
    tr.innerHTML = `
        <td>Sibling</td>
        <td><input type="text" name="sibling_last_name[]" class="form-control form-control-sm" required></td>
        <td><input type="text" name="sibling_first_name[]" class="form-control form-control-sm" required></td>
        <td><input type="text" name="sibling_middle_name[]" class="form-control form-control-sm"></td>
        <td><input type="number" name="sibling_age[]" class="form-control form-control-sm" style="max-width:70px;"></td>
        <td><input type="text" name="sibling_ethnic[]" class="form-control form-control-sm"></td>
        <td><input type="text" name="sibling_occupation[]" class="form-control form-control-sm"></td>
        <td>Living</td>
        <td><button type="button" class="btn btn-outline-danger btn-sm" onclick="this.closest('tr').remove()"><i class="fas fa-trash"></i></button></td>
    `;
    tbody.appendChild(tr);
}

function addAssistanceRow() {
    var tbody = document.getElementById("assistanceTableBody");
    if (!tbody) return;
    var tr = document.createElement("tr");
    tr.innerHTML = `
        <td><input type="text" name="ea_type[]" class="form-control form-control-sm" placeholder="e.g., LGU Grant" required></td>
        <td><input type="text" name="ea_grantee[]" class="form-control form-control-sm" placeholder="Grantee Name"></td>
        <td><input type="text" name="ea_institution[]" class="form-control form-control-sm" placeholder="Institution / Agency" required></td>
        <td><input type="number" step="0.01" name="ea_amount[]" class="form-control form-control-sm" placeholder="Amount (PHP)"></td>
        <td><button type="button" class="btn btn-outline-danger btn-sm" onclick="this.closest('tr').remove()"><i class="fas fa-trash"></i></button></td>
    `;
    tbody.appendChild(tr);
}

function toggleAssistanceSection(hasAssistance) {
    var sec = document.getElementById("assistanceTableSection");
    if (sec) {
        sec.style.display = hasAssistance ? "block" : "none";
    }
}
