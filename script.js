document.addEventListener("DOMContentLoaded", function() {
    const addProjectBtn = document.getElementById("addProjectBtn");
    const projectTableBody = document.querySelector("#projectTable tbody");

    addProjectBtn.addEventListener("click", function() {
        const projectName = prompt("Enter project name:");
        const projectStatus = prompt("Enter project status:");
        const projectDeadline = prompt("Enter project deadline (YYYY-MM-DD):");

        if (projectName && projectStatus && projectDeadline) {
            const newRow = document.createElement("tr");
            newRow.innerHTML = `
                <td>${projectName}</td>
                <td>${projectStatus}</td>
                <td>${projectDeadline}</td>
            `;
            projectTableBody.appendChild(newRow);
        } else {
            alert("All fields are required!");
        }
    });
});
