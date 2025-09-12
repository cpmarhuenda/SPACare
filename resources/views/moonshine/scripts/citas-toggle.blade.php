@push('scripts')
<script>
// DOCUMENTAR creado para que se muestren/oculten campos según el tipo de cita (puntual o recurrente)
document.addEventListener("DOMContentLoaded", function () {
    setTimeout(function () {
        const tipoCitaSelect = document.querySelector("select[name='tipo']");
        if (!tipoCitaSelect) return;

        function toggleCitaFields() {
            const esRecurrente = tipoCitaSelect.value === "recurrente";

            document.querySelectorAll("[data-recurrente]").forEach(field => {
                const wrapper = field.closest(".moonshine-field");
                if (wrapper) {
                    wrapper.style.display = esRecurrente ? "block" : "none";
                }
            });

            document.querySelectorAll("[data-puntual]").forEach(field => {
                const wrapper = field.closest(".moonshine-field");
                if (wrapper) {
                    wrapper.style.display = esRecurrente ? "none" : "block";
                }
            });
        }

        tipoCitaSelect.addEventListener("change", toggleCitaFields);
        toggleCitaFields(); // Ejecutar al cargar
    }, 300);
});
</script>
@endpush
