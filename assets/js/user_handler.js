document.addEventListener("DOMContentLoaded", function () {
  function showMessage(element, message, isSuccess) {
    element.textContent = message;
    element.classList.remove("d-none", "error-message", "success-message");
    element.classList.add(isSuccess ? "success-message" : "error-message");
  }

  function handleRegistrationSuccess() {
    const errorDiv = document.getElementById("addUserErrors");
    showMessage(errorDiv, "Registration successful! You can now login.", true);

    setTimeout(() => {
      const signInForm = document.querySelector(".sign-in-form");
      const signUpForm = document.querySelector(".sign-up-form");

      signUpForm.classList.remove("active");
      signInForm.classList.add("active");
    }, 1000);
  }

  // Handle role change button click
  document.querySelectorAll(".edit-role").forEach((button) => {
    button.addEventListener("click", function () {
      const uid = this.dataset.uid;
      document.getElementById("edit_uid").value = uid;
      const modal = new bootstrap.Modal(document.getElementById("roleModal"));
      modal.show();
    });
  });

  // Single handler for the role/edit form
  document.getElementById("roleForm").addEventListener("submit", function (e) {
    e.preventDefault();
    const formData = new FormData(this);
    const userData = {};

    // Convert FormData to JSON object, handling empty values
    formData.forEach((value, key) => {
      userData[key] = value || null;
    });

    const modal = bootstrap.Modal.getInstance(
      document.getElementById("roleModal")
    );

    fetch("../../server/query/update_user.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
      },
      body: JSON.stringify(userData),
    })
      .then(async (response) => {
        const text = await response.text();
        try {
          const data = JSON.parse(text);
          if (!response.ok) {
            throw new Error(
              data.message || `HTTP error! status: ${response.status}`
            );
          }
          return data;
        } catch (e) {
          console.error("Server response:", text);
          throw new Error("Server error: " + e.message);
        }
      })
      .then((data) => {
        if (data.status === "success") {
          alert("User information updated successfully");
          modal.hide();
          location.reload();
        } else {
          throw new Error(data.message || "Unknown error occurred");
        }
      })
      .catch((error) => {
        console.error("Error:", error);
        alert("Error updating user: " + error.message);
      });
  });

  // Update the edit user button click handler
  document.querySelectorAll(".edit-user").forEach((button) => {
    button.addEventListener("click", function () {
      const uid = this.dataset.uid;
      const row = this.closest("tr");

      // Fetch complete user details from server
      fetch(`../../server/query/get_user_details.php?uid=${uid}`)
        .then((response) => response.json())
        .then((user) => {
          // Populate all modal fields
          document.getElementById("edit_uid").value = user.uid;
          document.getElementById("edit_email").value = user.email;
          document.getElementById("edit_first_name").value = user.first_name;
          document.getElementById("edit_last_name").value = user.last_name;
          document.getElementById("edit_birth_date").value =
            user.birth_date || "";
          document.getElementById("edit_gender").value = user.gender || "";
          document.getElementById("edit_phone").value = user.phone || "";
          document.getElementById("edit_address").value = user.address || "";

          // Set select values
          document.querySelector('#roleModal select[name="role"]').value =
            user.role;
          document.querySelector('#roleModal select[name="type"]').value =
            user.type;
          document.querySelector('#roleModal select[name="status"]').value =
            user.status;

          // Show modal
          const modal = new bootstrap.Modal(
            document.getElementById("roleModal")
          );
          modal.show();
        })
        .catch((error) => {
          alert("Error loading user details. Please try again.");
        });
    });
  });

  // Handle save user button click
  document.querySelectorAll(".save-user").forEach((button) => {
    button.addEventListener("click", function () {
      const row = this.closest("tr");
      const uid = this.dataset.uid;

      const userData = {
        uid: uid,
        first_name: row.querySelector(".name-edit input:first-child").value,
        last_name: row.querySelector(".name-edit input:last-child").value,
        email: row.querySelector(".email-edit").value,
        role: row.querySelector(".role-edit").value,
        type: row.querySelector(".type-edit").value,
      };

      fetch("../../server/query/update_user.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
        },
        body: JSON.stringify(userData),
      })
        .then((response) => response.json())
        .then((data) => {
          if (data.status === "success") {
            // Update display
            row.querySelector(
              ".name-text"
            ).textContent = `${userData.first_name} ${userData.last_name}`;
            row.querySelector(".email-text").textContent = userData.email;
            row.querySelector(".role-text").textContent = userData.role;
            row.querySelector(".type-text").textContent = userData.type;

            // Reset view
            resetRowView(row);
            alert("User updated successfully");
          } else {
            alert("Error: " + data.message);
          }
        });
    });
  });

  // Handle cancel edit button click
  document.querySelectorAll(".cancel-edit").forEach((button) => {
    button.addEventListener("click", function () {
      const row = this.closest("tr");
      resetRowView(row);
    });
  });

  function resetRowView(row) {
    row
      .querySelectorAll(".name-text, .email-text, .role-text, .type-text")
      .forEach((el) => el.classList.remove("d-none"));
    row
      .querySelectorAll(".name-edit, .email-edit, .role-edit, .type-edit")
      .forEach((el) => el.classList.add("d-none"));
    row.querySelector(".edit-user").classList.remove("d-none");
    row.querySelector(".save-user").classList.add("d-none");
    row.querySelector(".cancel-edit").classList.add("d-none");
  }

  // Handle edit type button click
  document.querySelectorAll(".edit-type").forEach((button) => {
    button.addEventListener("click", function () {
      const row = this.closest("tr");
      const typeCell = row.querySelector(".type-cell");
      const typeText = typeCell.querySelector(".type-text");
      const typeEdit = typeCell.querySelector(".type-edit");
      const saveButton = row.querySelector(".save-type");

      // Show edit mode
      typeText.classList.add("d-none");
      typeEdit.classList.remove("d-none");
      this.classList.add("d-none");
      saveButton.classList.remove("d-none");

      // Set current value
      typeEdit.value = typeText.textContent.trim();
    });
  });

  // Handle save type button click
  document.querySelectorAll(".save-type").forEach((button) => {
    button.addEventListener("click", function () {
      const row = this.closest("tr");
      const uid = this.dataset.uid;
      const typeEdit = row.querySelector(".type-edit");
      const newType = typeEdit.value;

      fetch("../../server/query/update_user_type.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/x-www-form-urlencoded",
        },
        body: `uid=${uid}&type=${newType}`,
      })
        .then((response) => response.json())
        .then((data) => {
          if (data.status === "success") {
            const typeText = row.querySelector(".type-text");
            const editButton = row.querySelector(".edit-type");

            // Update display
            typeText.textContent = newType;
            typeText.classList.remove("d-none");
            typeEdit.classList.add("d-none");
            this.classList.add("d-none");
            editButton.classList.remove("d-none");

            alert("User type updated successfully");
          } else {
            alert("Error: " + data.message);
          }
        });
    });
  });

  // Handle add user form submission
  document
    .getElementById("addUserForm")
    .addEventListener("submit", function (e) {
      e.preventDefault();
      const formData = new FormData(this);
      const submitBtn = this.querySelector('button[type="submit"]');
      const errorDiv = document.getElementById("addUserErrors");

      submitBtn.disabled = true;
      submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding...';
      errorDiv.classList.add("d-none");

      fetch("../../server/query/add_user.php", {
        method: "POST",
        body: formData,
      })
        .then((response) => response.json())
        .then((data) => {
          if (data.status === "success") {
            handleRegistrationSuccess();
          } else {
            showMessage(errorDiv, data.message, false);
          }
        })
        .catch((error) => {
          showMessage(errorDiv, "An error occurred. Please try again.", false);
        })
        .finally(() => {
          submitBtn.disabled = false;
          submitBtn.innerHTML = "Add User";
        });
    });

  // Add this new event listener for delete buttons
  document.querySelectorAll(".delete-user").forEach((button) => {
    button.addEventListener("click", function () {
      if (
        confirm(
          "Are you sure you want to delete this user? This action cannot be undone."
        )
      ) {
        const uid = this.dataset.uid;
        fetch("../../server/query/delete_user.php", {
          method: "POST",
          headers: {
            "Content-Type": "application/json",
          },
          body: JSON.stringify({
            uid: uid,
          }),
        })
          .then((response) => response.json())
          .then((data) => {
            if (data.status === "success") {
              this.closest("tr").remove();
              alert("User deleted successfully");
            } else {
              alert("Error: " + data.message);
            }
          });
      }
    });
  });
});
