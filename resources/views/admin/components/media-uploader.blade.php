<div id="mediaManager" class="media-modal">

    <div class="media-container">

        <!-- LEFT SIDEBAR -->
        <div class="media-sidebar">

            <h3 class="media-title">Media Library</h3>

            <div class="library-section">

                <div class="library-item active" onclick="loadMedia()">
                    All Photos
                </div>

            </div>

        </div>


        <!-- MAIN AREA -->
        <div class="media-content">

            <div class="media-header">

                <h4>All Photos</h4>

                <button onclick="closeMediaManager()" class="media-close">
                    Close
                </button>

            </div>


            <div class="media-grid" id="mediaGrid">

                <!-- Upload tile -->
                <div class="upload-tile">

                    <input type="file" id="mediaUploadInput">

                    <div class="upload-box">

                        <div class="upload-icon">☁</div>

                        <p>Upload Media</p>

                        <span>Click or drag and drop</span>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>