import os
from os.path import exists, isfile, isdir, realpath, dirname


class Image:
    REPOSITORY_PATH_KEY = "repository_path"
    NAME_KEY = "name"
    TAG_KEY = "tag"
    BUILD_SOURCE_KEY = "build_source"
    BUILD_WORK_DIR_KEY = "build_work_dir"

    def __init__(
        self,
        repositoryPath,
        name,
        tag="latest",
        build_source="",
        build_working_directory=""
    ):
        self.repositoryPath = repositoryPath
        self.name = name
        self.tag = tag
        self.build_source = build_source
        self.build_working_directory = build_working_directory

        cwd = os.getcwd()

        if build_source.strip() == "":
            self.build_source = cwd + "/Dockerfile"

        if build_working_directory.strip() == "":
            self.build_working_directory = cwd

        errors = []
        toCheck = {self.BUILD_SOURCE_KEY: self.build_source,
                   self.BUILD_WORK_DIR_KEY: self.build_working_directory}

        for key, path in toCheck.items():
            if exists(path) is False:
                errors.append("[{0}]({1}) does not exist.".format(key, path))
                continue

            if os.access(path, os.R_OK) is False:
                errors.append("[{0}]({1}) is not readable.".format(key, path))

        if isfile(self.build_source) is False:
            errors.append(
                "[{0}]({1}) is not a file.".format(
                    self.BUILD_SOURCE_KEY,
                    self.build_source
                )
            )

        if isdir(self.build_working_directory) is False:
            errors.append(
                "[{0}]({1}) is not a directory.".format(
                    self.BUILD_WORK_DIR_KEY,
                    self.build_working_directory
                )
            )

        if os.access(self.build_working_directory, os.W_OK) is False:
            errors.append(
                "[{0}]({1}) is not writable.".format(
                    key,
                    self.build_working_directory
                )
            )

        if len(errors) != 0:
            raise OSError(' '.join(errors))

    def full_path(self):
        return "{0}/{1}:{2}".format(self.repositoryPath, self.name, self.tag)


class PopDockerImage(Image):
    def __init__(self, thing, version):
        dockerfiles_path = dirname(realpath(__file__)) + "/../../Dockerfiles"
        imagePath = "{0}/{1}{2}.Dockerfile".format(
            dockerfiles_path,
            thing,
            version
        )

        super().__init__(
            'localhost/' + thing,
            thing,
            version,
            imagePath,
            dockerfiles_path
        )


def image_to_hashmap(img):
    return {Image.REPOSITORY_PATH_KEY: img.repositoryPath,
            Image.NAME_KEY: img.name,
            Image.TAG_KEY: img.tag,
            Image.BUILD_SOURCE_KEY: realpath(img.build_source),
            Image.BUILD_WORK_DIR_KEY: realpath(img.build_working_directory)}


def hashmap_to_image(m):
    return Image(
            m[Image.REPOSITORY_PATH_KEY],
            m[Image.NAME_KEY],
            m[Image.TAG_KEY],
            m[Image.BUILD_SOURCE_KEY],
            m[Image.BUILD_WORK_DIR_KEY])
