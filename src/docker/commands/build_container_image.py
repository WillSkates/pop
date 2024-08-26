class BuildContainerImage:
    def __init__(self, image):
        self.image = image

    def cmd(self):
        return [
            'build',
            '-t',
            self.image.full_path(),
            '-f',
            self.image.build_source,
            self.image.build_working_directory
        ]
