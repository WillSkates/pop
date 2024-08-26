import cli
import config
import docker


class Version:
    def run(self, args):
        if len(args) < 2:
            raise ValueError(
                "Build needs 2 arguments, one for the thing "
                + "the second is the version of the thing we "
                + "want to use for our build."
            )

        thing = args[0]
        version = args[1]

        image = docker.PopDockerImage(thing, version)

        config.pop_config_set(thing + '.image', docker.image_to_hashmap(image))

        return 0
