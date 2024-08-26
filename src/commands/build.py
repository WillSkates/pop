import cli
import docker
from os.path import realpath, dirname


class Build:
    def run(self, args):
        self.args = args

        if len(args) < 2:
            raise ValueError(
                "Build needs 2 arguments, one for "
                + "the thing and one for the version "
                + "of the thing that we want to build"
            )

        thing = args[0]
        version = args[1]

        image = docker.PopDockerImage(thing, version)

        build_cmd = docker.commands.BuildContainerImage(image)
        dockerfiles_path = dirname(realpath(__file__)) + "/../../Dockerfiles"

        to_run = cli.CLICommand("podman", build_cmd.cmd(), dockerfiles_path)

        return to_run.go()
