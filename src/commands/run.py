import cli
import config
import docker
import hashlib
import os


class Run:
    def run(self, args):
        self.args = args

        if len(args) < 2:
            raise ValueError(
                "Run needs atleast 2 arguments, one for the thing you want"
                + "to run and then the command."
            )

        thing = str(args[0])

        cwd = os.getcwd()

        m = hashlib.sha1(usedforsecurity=False)
        m.update(str.encode(cwd))

        cwdhash = m.hexdigest()

        details = config.pop_config_get(thing + ".image")
        image = docker.hashmap_to_image(details)

        runcmd = docker.commands.RunInContainer(
            image,
            args,
            cwdhash + "-" + image.name,
            {cwd: "/work"},
            {"CI": "true"},
            True,
            False
        )

        to_run = cli.CLICommand("podman", runcmd.cmd(), cwd)

        return to_run.go()
