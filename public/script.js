// const videosCollectionHolder = document.querySelector("#trick_videos");

// let indexVideos = videosCollectionHolder.querySelectorAll("div").length;

// const addVideo = (e) => {
//     console.log(indexVideos);
//     videosCollectionHolder.innerHTML += videosCollectionHolder.dataset.prototype.replace(/__name__/g, indexVideos);
//     indexVideos++;
// };

// const btnAddVideo = document.querySelector("#js-new-video");

// btnAddVideo?.addEventListener("click", (e) => addVideo(e));

const newItem = (e) => {
    const collectionHolder = document.querySelector(e.currentTarget.dataset.collection);

    const item = document.createElement("div");
    item.classList.add("item");
    item.innerHTML = collectionHolder.dataset.prototype.replace(/__name__/g, collectionHolder.dataset.index);
    item.querySelector(".js-btn-remove").addEventListener("click", () => item.remove());
    collectionHolder.appendChild(item);

    collectionHolder.dataset.index++;
};

document.querySelectorAll(".js-btn-new").forEach((btn) => {
    btn.addEventListener("click", newItem);
});

document.querySelectorAll(".js-btn-remove").forEach((btn) => {
    btn.addEventListener("click", (e) => e.currentTarget.closest("div").remove());
});
