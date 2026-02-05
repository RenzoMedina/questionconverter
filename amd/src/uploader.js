/**
 * Uploader module for local_questionconverter plugin.
 * @module local_questionconverter/uploader
 * @copyright 2026 Renzo Medina <medinast30@gmail.com>
 * @license http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

export const init = () => {
  const loader = document.getElementById("qc-loader");
  const progressBar = document.getElementById("qc-progress-bar");
  const moodleForm = document.querySelector(".qc-moodle-form-wrapper form");
  let progressTimer = null;
  let progressValue = 10;
  let progressDirection = 1;

  const startProgress = () => {
    if (!progressBar || progressTimer) {
      return;
    }
    progressValue = 10;
    progressDirection = 1;
    progressBar.style.transition = "width 0.2s ease";
    progressBar.style.width = progressValue + "%";
    progressTimer = setInterval(function () {
      if (!loader || loader.classList.contains("hidden")) {
        stopProgress();
        return;
      }
      progressValue += progressDirection * 8;
      if (progressValue >= 90) {
        progressValue = 90;
        progressDirection = -1;
      } else if (progressValue <= 10) {
        progressValue = 10;
        progressDirection = 1;
      }
      progressBar.style.width = progressValue + "%";
    }, 200);
  };
  const stopProgress = () => {
    if (progressTimer) {
      clearInterval(progressTimer);
      progressTimer = null;
    }
    if (progressBar) {
      progressBar.style.width = "10%";
    }
  };
  if (loader && moodleForm) {
    moodleForm.addEventListener("submit", function () {
      if (
        typeof moodleForm.checkValidity === "function" &&
        !moodleForm.checkValidity()
      ) {
        loader.classList.add("hidden");
        loader.classList.remove("flex");
        stopProgress();
        return;
      }
      loader.classList.remove("hidden");
      loader.classList.add("flex");
      startProgress();
    });
  }
};
